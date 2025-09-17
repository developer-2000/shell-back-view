<?php

namespace App\Repositories;

use App\Events\AddNewMessageEvent;
use App\Events\UpdateMessageEvent;
use App\Events\WhoReadEvent;
use App\Jobs\OneMinuteCheckUnreadMessagesJob;
use App\Jobs\SendPromotionEmailsToPrinters;
use App\Models\DesignChat;
use App\Models\PrintPromotionReport;
use App\Models\Promotion;
use App\Models\PromotionSurfaceDesign;
use App\Models\User;
use App\Services\SocketReverbService;
use App\Services\XlFileService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DesignChatRepository extends BaseRepository {

    /**
     * Выборка Дизайн чата по id
     * @param array $validatedData
     * @param User $currentUser
     * @return array
     */
    public function getChat(array $validatedData, User $currentUser): array {
        try {
            // Поиск чата по ID
            $chat = DesignChat::find($validatedData['chat_id']);

            // Получаем текущий массив socket_users_ids
            $socketUsersIds = $chat->socket_users_ids;

            // Добавить участника чата для socket
            if (!in_array($currentUser->id, $socketUsersIds)) {
                // Добавляем ID пользователя
                $socketUsersIds[] = $currentUser->id;
                $chat->socket_users_ids = $socketUsersIds;
                // Сохраняем изменения в базе
                $chat->save();
            }

            // Возвращаем успешный ответ с данными чата
            return [
                'success' => true,
                'status_code' => 200,
                'message' => 'Chat retrieved successfully.',
                'data' => $chat->toArray()
            ];

        }
        catch (\Exception $e) {
            // Обработка исключений
            return [
                'success' => false,
                'status_code' => 500,
                'message' => 'An error occurred while retrieving the chat.',
            ];
        }
    }

    /**
     * Создать новое сообщение в чате
     * @param array $validatedData
     * @param User $currentUser
     * @return array
     */
    public function createMessage(array $validatedData, User $currentUser): array {
        DB::beginTransaction();
        try {
            // Находим запись чата
            $designChat = DesignChat::where("id", $validatedData['chat_id'])
                ->with("promotionSurfaceDesign.promotion")->first();
            $switch_file = null;

            // Извлекаем массив сообщений
            $messages = $designChat->messages ?? [];

            // Создаем новый объект сообщения
            $newMessage = [
                "id" => $this->generateUniqueId($messages),
                'text' => $validatedData['text'] ?? null,
                'url_file' => $validatedData['url_file'] ?? null,
                'file_name' => $validatedData['file_name'] ?? null,
                'file_size' => $validatedData['file_size'] ?? null,
                'url_images' => [],
                'type_extension' => $validatedData['type_extension'] ?? null,
                'type_file' => $validatedData['type_file'] ?? null,
                'comment_mess_id' => $validatedData['comment_mess_id'] ?? null,
                'update_message' => $validatedData['update_message'] ?? null,
                'delete_message' => false,
                'who_read_messages' => [$currentUser->id],
                "sender_id" => $currentUser->id,
                "rating_file" => null,
                "create_date" => Carbon::now(),
            ];

            // Определяем, в какую директорию сохранять файл
            $switch_file = $newMessage['type_extension']."s";

            // удалить предыдущий файл с таким статусом
            if ($validatedData['type_file'] === "HQ" || $validatedData['type_file'] === "LQ") {
                $this->markLastDeleted($messages, $newMessage['type_extension'], $validatedData['type_file']);
            }

            // 1 Сохранить image в хранилище
            if($switch_file === "images"){
                // Перебираем make_size для обработки размеров
                $imageUrls = [];

                foreach ($validatedData['make_size'] as $size) {
                    // Уникальное имя файла для каждого размера
                    $sizeSuffix = '';
                    if ($size === "original") {
                        $sizeSuffix = 'original';
                    }
                    elseif (preg_match('/^w_(\d+)$/', $size, $matches)) {
                        $width = (int)$matches[1];
                        $sizeSuffix = 'w_' . $width;
                    }

                    // Сохранить изображение с учетом размера
                    $uploadResult = $this->processUploadForDesignChat(
                        $newMessage['file_name'],
                        $newMessage['url_file'],
                        'uploads/design-chat/design-chat-id-' . $newMessage['id'] . '/' . $switch_file . '/' . $sizeSuffix,
                        $size
                    );

                    $imageUrls[$size] = $uploadResult['new_url'];
                }

                $newMessage['url_images'] = $imageUrls;
                $newMessage['url_file'] = "";
            }
            // 1 Сохранить document в хранилище
            if($switch_file === "documents"){
                $uploadResult = $this->processUploadForDesignChat(
                    $newMessage['file_name'],
                    $newMessage['url_file'],
                    'uploads/design-chat/design-chat-id-' . $newMessage['id'] . '/' . $switch_file,
                    null
                );

                if (!$uploadResult['success']) {
                    DB::rollBack();
                    return $uploadResult;
                }

                $newMessage['url_file'] = $uploadResult['new_url'];
            }

            $messages[] = $newMessage;
            $designChat->messages = $messages;
            $designChat->save();

            // 2 Если file был со статусом HQ
            if($newMessage['type_file'] === "HQ"){
                // 1 Обновляем статус Дизайна на "Completed"
                $repository = new PromotionSurfacesDesignRepository();
                $updateResult = $repository->updateDesignStatus($designChat['id'], 2);

                // Проверяем результат обновления статуса
                if (!$updateResult['success']) {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'status_code' => 500,
                        'message' => 'Failed to update brief design status: ' . $updateResult['message'],
                    ];
                }

                // 2 Если Дизайн стал Completed после того как менеджер активировал Promotion для Printer
                // Добавить новый дизайн в XL данные Printer и отправить ему Email
                $surfaceDesign = PromotionSurfaceDesign::where("chat_id", $validatedData['chat_id'])
                    ->with('surface.printer')
                    ->first();
                $printPromotion = PrintPromotionReport::where("promotion_id", $surfaceDesign->promotion_id)
                    ->first();

                if($printPromotion){
                    $arrData = [
                        'promotion_id' => $surfaceDesign->promotion_id,
                        'promotion_name' => "",
                        'display_address' => true,
                        'display_categories' => false,
                        'number_percent' => $printPromotion->percent,
                    ];

                    $xlFileService = new XlFileService($arrData);
                    $xlFileService->generateXLDate();
                    $objectDb = $xlFileService->getObjectDb();

                    // Обновить данные для Printer
                    PrintPromotionReport::where(['promotion_id' => $surfaceDesign->promotion_id])
                        ->update(['surfaces' => $objectDb]);

                    $printer = $surfaceDesign['surface']['printer'];
                    $printer = [[
                        'name' => $printer['name'],
                        'email' => $printer['email'],
                    ]];

                    // Отправить письмо Printer чьй Design получил Completed
                    dispatch(new SendPromotionEmailsToPrinters($printer, $surfaceDesign->promotion_id));
                }

                // 3 Если Дизайн стал Completed последним из всех дизайнов Promotion
                // Изменить статус этого Promotion на designer completed
                $surfaceDesigns = PromotionSurfaceDesign::where('promotion_id', $surfaceDesign->promotion_id)
                    ->where('chat_id', '!=', $validatedData['chat_id'])
                    ->get();

                // Проверяем, чтобы у всех остальных дизайнов статус был "Completed"
                $boolCompleted = true;
                foreach ($surfaceDesigns as $design) {
                    if (isset($design->data['status']) && $design->data['status'] != 'Completed') {
                        $boolCompleted = false;
                        break;
                    }
                }

                if($boolCompleted){
                    $promotion = Promotion::find($surfaceDesign->promotion_id);
                    // Установка в статус Promotion - designer completed
                    $promotion->setDesignerCompletedStatus();
                }

            }

            // 3 SOCKET Push отправки этого сообщения всем участникам чата.
            $socketService = (new SocketReverbService($currentUser));
            $sendResult = $socketService->sendingMessagesToParticipants(
                $designChat->socket_users_ids,
                $newMessage,
                AddNewMessageEvent::class
            );

            // Проверяем успешность отправки сообщений
            if (!$sendResult['success']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'status_code' => $sendResult['status_code'],
                    'message' => $sendResult['message'],
                ];
            }

            // 4 Уведомление собеседника по Email
            // Установка задачи 1 мин - Проверка не прочитаности сообщения автора
            $this->makeJobOneMinuteUnreadMessage($currentUser, $designChat, $newMessage["id"]);

            DB::commit();
            return [
                'success' => true,
                'status_code' => 200,
                'message' => 'Message successfully created'
            ];
        }
        catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'status_code' => 500,
                'message' => 'Failed to create message: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Обновить свое сообщение в чате
     * @param array $validatedData
     * @return array
     */
    public function updateMessage(array $validatedData, User $currentUser): array {
        DB::beginTransaction();

        try {
            $designChat = DesignChat::where("id", $validatedData['chat_id'])
                ->with("promotionSurfaceDesign.promotion")->first();

            if (empty($designChat->messages)) {
                return [
                    'success' => false,
                    'status_code' => 404,
                    'message' => 'No messages found in the chat.',
                ];
            }

            // Извлекаем message_id из входящих данных
            $messageId = $validatedData['message_id'] ?? null;
            if (!$messageId) {
                return [
                    'success' => false,
                    'status_code' => 400,
                    'message' => 'Message ID is required.',
                ];
            }

            // Находим индекс сообщения по его ID
            $messageIndex = array_search($messageId, array_column($designChat->messages, 'id'));
            if ($messageIndex === false) {
                return [
                    'success' => false,
                    'status_code' => 404,
                    'message' => 'Message not found.',
                ];
            }

            $messages = $designChat->messages; // Получаем текущее значение messages

            // 1 Обновляем свойства сообщения
            $messages[$messageIndex]['text'] = $validatedData['text'] ?? $messages[$messageIndex]['text'];
            $messages[$messageIndex]['type_file'] = $validatedData['type_file'] ?? $messages[$messageIndex]['type_file'];
            $messages[$messageIndex]['comment_mess_id'] = $validatedData['comment_mess_id'] ?? $messages[$messageIndex]['comment_mess_id'];
            $messages[$messageIndex]['update_message'] = true;

            $designChat->messages = $messages; // Устанавливаем новое значение
            $designChat->save();

            // 2 Обновляем статус brief дизайна на "Completed"
            if($messages[$messageIndex]['type_file'] === "HQ"){
                $repository = new PromotionSurfacesDesignRepository();
                $updateResult = $repository->updateDesignStatus($designChat['id'], 2);

                // Проверяем результат обновления статуса
                if (!$updateResult['success']) {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'status_code' => 500,
                        'message' => 'Failed to update brief design status: ' . $updateResult['message'],
                    ];
                }
            }

            // 3 SOCKET Отправка сообщений всем участникам чата.
            $socketService = (new SocketReverbService($currentUser));
            $sendResult = $socketService->sendingMessagesToParticipants(
                $designChat->socket_users_ids,
                $messages[$messageIndex],
                UpdateMessageEvent::class
            );

            // Проверяем успешность отправки сообщений
            if (!$sendResult['success']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'status_code' => $sendResult['status_code'],
                    'message' => $sendResult['message'],
                ];
            }

            // 4 Установка задачи 1 мин - Проверка не прочитаности сообщения автора
            $this->makeJobOneMinuteUnreadMessage($currentUser, $designChat, $messages[$messageIndex]['id']);

            DB::commit();
            return [
                'success' => true,
                'status_code' => 200,
                'message' => 'Message successfully updated',
            ];

        }
        catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'status_code' => 500,
                'message' => 'Failed to update message: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Удалить свое сообщение в чате
     * @param array $validatedData
     * @return array
     */
    public function deleteMessage(array $validatedData, User $currentUser): array {
        DB::beginTransaction();

        try {
            // Получаем чат
            $chat = DesignChat::find($validatedData['chat_id']);
            $messages = $chat->messages;

            // Находим индекс сообщения
            $messageIndex = array_search($validatedData['message_id'], array_column($messages, 'id'));

            // Проверяем, существует ли сообщение
            if ($messageIndex === false) {
                return [
                    'success' => false,
                    'status_code' => 404,
                    'message' => 'Message not found.',
                ];
            }

            // 1 Отметка удаленного сообщения
            $messages[$messageIndex]['delete_message'] = true;

            // Обновляем сообщения в чате
            $chat->messages = $messages;
            $chat->save();

            // 2 Обновляем статус brief дизайна на "Approved"
            if (
                $messages[$messageIndex]['type_file'] === "HQ" &&
                ($messages[$messageIndex]['type_extension'] === "image" || $messages[$messageIndex]['type_extension'] === "document")
            ) {
                $repository = new PromotionSurfacesDesignRepository();
                $updateResult = $repository->updateDesignStatus($chat['id'], 1);

                // Проверяем результат обновления статуса
                if (!$updateResult['success']) {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'status_code' => $updateResult['status_code'],
                        'message' => 'Failed to update brief design status: ' . $updateResult['message'],
                    ];
                }
            }

            // 3 SOCKET Отправка сообщений всем участникам чата.
            $socketService = (new SocketReverbService($currentUser));
            $sendResult = $socketService->sendingMessagesToParticipants(
                $chat->socket_users_ids,
                $messages[$messageIndex],
                UpdateMessageEvent::class
            );

            // Проверяем успешность отправки сообщений
            if (!$sendResult['success']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'status_code' => $sendResult['status_code'],
                    'message' => $sendResult['message'],
                ];
            }

            DB::commit();
            return [
                'success' => true,
                'status_code' => 200,
                'message' => 'Message successfully deleted',
            ];

        }
        catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'status_code' => 500,
                'message' => 'Failed to delete message: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Установить во всем чате статус - мной прочитано
     * @param array $validatedData
     * @param User $currentUser
     * @return array
     */
    public function setReadStatusMessages(array $validatedData, User $currentUser): array {
        // Начинаем транзакцию
        DB::beginTransaction();

        try {
            // Получаем чат по ID
            $chat = DesignChat::find($validatedData['chat_id']);
            $messages = $chat->messages;

            // Не массив или нет сообщений
            if (!$messages || !is_array($messages)) {
                return [
                    'success' => true,
                    'message' => 'No messages found in chat.',
                    'status_code' => 200
                ];
            }

            // 1 Добавить мой id в массив прочитанных сообщений
            foreach ($messages as &$message) {
                // Автор не я
                if ($message['sender_id'] !== $currentUser->id) {
                    // Моего id нет в массиве
                    if (!in_array($currentUser->id, $message['who_read_messages'])) {
                        // Добавить мой id
                        $message['who_read_messages'][] = $currentUser->id;
                    }
                }
            }

            // Сохраняем измененные сообщения
            $chat->messages = $messages;

            // 2 Почистить фиксацию отправки мне Email в случае если я не читал чат
            $currentSendEmailUserIds = $chat->send_email_user_ids ?? [];
            // Удаляем ваш ID из массива
            $updatedSendEmailUserIds = array_filter($currentSendEmailUserIds, function ($userId) use ($currentUser) {
                return $userId != $currentUser->id;
            });
            // Обновляем свойство объекта и сохраняем
            $updatedSendEmailUserIds = array_values($updatedSendEmailUserIds);
            $chat->send_email_user_ids = $updatedSendEmailUserIds;
            $chat->save();

            // 3 SOCKET Отправка сообщений всем участникам чата.
            $socketService = (new SocketReverbService($currentUser));
            $sendResult = $socketService->sendingMessagesToParticipants(
                $chat->socket_users_ids,
                ["read_user_id" => $currentUser->id],
                WhoReadEvent::class
            );

            // Проверяем успешность отправки сообщений
            if (!$sendResult['success']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'status_code' => $sendResult['status_code'],
                    'message' => $sendResult['message'],
                ];
            }

            // Подтверждаем транзакцию
            DB::commit();

            return [
                'success' => true,
                'message' => 'Messages read status updated successfully.',
                'status_code' => 200
            ];

        } catch (\Exception $e) {
            // Откатываем транзакцию в случае ошибки
            DB::rollBack();
            return [
                'success' => false,
                'status_code' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Установить rating image менеджером
     * И статус дизайна этого чата
     * @param array $validatedData
     * @return array
     */
    public function updateSwitchRatingImage(array $validatedData, User $currentUser): array {
        DB::beginTransaction();

        try {
            $chat = DesignChat::find($validatedData['chat_id']);
            $rating = $validatedData['rating'];

            // Находим индекс сообщения в массиве сообщений
            $messages = $chat->messages;
            $messageIndex = array_search($validatedData['message_id'], array_column($messages, 'id'));

            if ($messageIndex === false) {
                return [
                    'success' => false,
                    'message' => "Message with id not found.",
                    'status_code' => 404
                ];
            }

            // 1 Обновляем статус картинки в сообщении
            $messages[$messageIndex]['rating_file'] = $rating;

            // Установить индекс массива статусов
            $statusDesignNum = 0;
            if ($rating == "Approved") {
                $statusDesignNum = 1;
            }

            // 2 Обновляем статус дизайна
            $repository = new PromotionSurfacesDesignRepository();
            $updateResult = $repository->updateDesignStatus($validatedData['chat_id'], $statusDesignNum);

            if (!$updateResult['success']) {
                DB::rollBack();
                return $updateResult;
            }

            // Сохраняем обновленные сообщения обратно в чат
            $chat->messages = $messages;
            $chat->save();

            // 3 SOCKET Отправка сообщений всем участникам чата.
            $socketService = (new SocketReverbService($currentUser));
            $sendResult = $socketService->sendingMessagesToParticipants(
                $chat->socket_users_ids,
                $messages[$messageIndex],
                UpdateMessageEvent::class
            );

            // Проверяем успешность отправки сообщений
            if (!$sendResult['success']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'status_code' => $sendResult['status_code'],
                    'message' => $sendResult['message'],
                ];
            }

            DB::commit();
            return [
                'success' => true,
                'message' => "Rating image updated successfully.",
                'status_code' => 200
            ];
        }
        catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to update rating image: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Генерация уникального идентификатора
     * @param array $messages
     * @return int
     */
    private function generateUniqueId(array $messages): int {
        // количество сообщений в массиве
        $count = count($messages);
        // Генерируем новый идентификатор
        return $count + 1;
    }

    /**
     * Пометить последнее сообщение как удаленное
     *
     * @param array $messages
     * @param string $typeExtension
     * @param string $typeFile
     * @return void
     */
    private function markLastDeleted(array &$messages, string $typeExtension, string $typeFile): void {
        for ($i = count($messages) - 1; $i >= 0; $i--) {
            if (
                $messages[$i]['type_extension'] == $typeExtension &&
                $messages[$i]['type_file'] == $typeFile
            ) {
                $messages[$i]['delete_message'] = true;
                break;
            }
        }
    }

    /**
     * Установка задачи 1 мин - Проверка не прочитаности сообщения автора
     *
     * @param User $currentUser
     * @param DesignChat $designChat
     * @param int $messageId
     * @return void
     */
    private function makeJobOneMinuteUnreadMessage(User $currentUser, DesignChat $designChat, int $messageId): void {
        // Массив Ids авторов сообщений поставленных задач
        $user_ids = $designChat->job_timer_user_ids;

        // Id автора сообщения нет в массиве ids поставленных задач
        if (is_array($user_ids) && !in_array($currentUser->id, $user_ids)) {
            // ID создателя Promotion (CM)
            $whoCreatedPromotionId = optional(optional($designChat->promotionSurfaceDesign)->promotion)->who_created_id;
            $surfaceDesign = PromotionSurfaceDesign::where("chat_id", $designChat->id)->first();
            // ID дизайнера дизайна
            $designerId = $surfaceDesign ? $surfaceDesign->designer_id : null;

            // Добавить id участников чата в массив
            $notifyUserIds = [];
            if (is_numeric($whoCreatedPromotionId)) {
                $notifyUserIds[] = (int) $whoCreatedPromotionId;
            }
            if (is_numeric($designerId)) {
                $notifyUserIds[] = (int) $designerId;
            }

            // 1 Фильтрация участников чата
            // Удалить тех кому уже было отправлено сообщение
            $sendEmailUserIds = $designChat->send_email_user_ids;
            if (is_array($sendEmailUserIds) && count($sendEmailUserIds) > 0) {
                $notifyUserIds = array_diff($notifyUserIds, $sendEmailUserIds);
            }
            // Удаление текущего пользователя из массива
            if (in_array($currentUser->id, $notifyUserIds)) {
                $notifyUserIds = array_diff($notifyUserIds, [$currentUser->id]);
            }

            // Есть кому слать Email
            if($notifyUserIds){
                // Добавляем ID текущего пользователя в массив поставленных задач
                $user_ids[] = $currentUser->id;
                $designChat->job_timer_user_ids = $user_ids;
                $designChat->save();

                dispatch(new OneMinuteCheckUnreadMessagesJob(
                    $currentUser->id,  // Автор сообщения
                    $notifyUserIds,    // Собеседники
                    $designChat->id,   // Чат ID
                    $messageId,        // ID сообщения
                ))->delay(now()->addMinutes(1));
            }
        }
    }

}
