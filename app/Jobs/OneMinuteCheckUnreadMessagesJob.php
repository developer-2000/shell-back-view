<?php

namespace App\Jobs;

use App\Mail\SendEmailToUsersAboutNewMessageInChatMail;
use App\Mail\SentUserAboutTrackerDistributorMail;
use App\Models\DesignChat;
use App\Models\Test;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class OneMinuteCheckUnreadMessagesJob implements ShouldQueue {
    use Queueable, SerializesModels;

    protected $authorId;
    protected $notifyUserIds;
    protected $chatId;
    protected $messageId;
    protected $timer;

    public function __construct(int $authorId, array $notifyUserIds, int $chatId, int $messageId, int $timer = 1) {
        $this->authorId = $authorId;
        $this->notifyUserIds = $notifyUserIds;
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->timer = $timer;
    }

    public function handle() {
        // Получаем чат по ID
        $designChat = DesignChat::where("id", $this->chatId)
            ->with("promotionSurfaceDesign.promotion","promotionSurfaceDesign.surface","promotionSurfaceDesign.design")
            ->first();

        if ($designChat && is_array($designChat->messages)) {
            // Ищем сообщение по messageId
            $message = collect($designChat->messages)->firstWhere('id', $this->messageId);

            if ($message) {
                // Проверяем, прочитано ли сообщение всеми пользователями
                $allNotifiedRead = $this->checkAllNotifiedRead($message);

                // 1 Сообщение не прочитано всеми (CM, Designer)
                if (!$allNotifiedRead) {
                    // Выставить задачу на 9 мин
                    if ($this->timer == 1) {
                        $this->dispatchJobForNextMessage($this->messageId, 9);
                    }
                    // Job прошел полный цикл - Отправка всем не читавшим чат участникам - Email
                    else{
                        // Массив для фиксации участников, которые не читали сообщения
                        $unreadUserIds = [];

                        // Перебор всех сообщений чата, начиная с текущего вместе с текущим
                        $messagesFromCurrent = collect($designChat->messages)->filter(function ($msg) {
                            return $msg['id'] >= $this->messageId;
                        });

                        // Проверка всех сообщений на наличие непрочитавших пользователей
                        foreach ($messagesFromCurrent as $msg) {
                            foreach ($this->notifyUserIds as $userId) {
                                if (!in_array($userId, $msg['who_read_messages'])) {
                                    $unreadUserIds[] = $userId;
                                }
                            }
                        }

                        // Убираем повторяющиеся ID
                        $unreadUserIds = array_unique($unreadUserIds);

                        // 1 Если есть пользователи, которые не читали, отправляем email
                        if (!empty($unreadUserIds)) {
                            // Формирование заголовка чата
                            $promotionSurfaceDesign = optional($designChat->promotionSurfaceDesign);
                            $titleChat = "{$promotionSurfaceDesign->promotion->name} - " .
                                "{$promotionSurfaceDesign->surface->name} - " .
                                "{$promotionSurfaceDesign->design->name}";

                            // Формирование ссылки на чат
                            $link = "https://new.st1shop.no/promotion-design?" . http_build_query([
                                    'prom_id'         => optional($promotionSurfaceDesign->promotion)->id,
                                    'sur_id'          => optional($promotionSurfaceDesign->surface)->id,
                                    'prom_sur_des_id' => $promotionSurfaceDesign->id
                                ]);

                            $emails = User::whereIn('id', $unreadUserIds)->pluck('email')->toArray();

                            // 1 Отправка писем всем участникам
                            foreach ($emails as $email) {
                                Mail::to($email)->send(new SendEmailToUsersAboutNewMessageInChatMail(
                                    $titleChat,   // Заголовок чата
                                    $link         // Ссылка на чат
                                ));
                            }

                            // 2 Зафиксировать этих участников - им были отправлены Emails
                            $currentEmailUserIds = is_array($designChat->send_email_user_ids) ? $designChat->send_email_user_ids : [];
                            $updatedEmailUserIds = array_unique(array_merge($currentEmailUserIds, $unreadUserIds));
                            // Обновление свойства и сохранение
                            $designChat->send_email_user_ids = $updatedEmailUserIds;
                            $designChat->save();
                        }

                        // 2 Удаляем ID автора из массива job_timer_user_ids поставленных задач
                        $this->removeAuthorFromTimerUserIds($designChat);
                    }
                }
                // 2 Сообщение прочитано всеми участниками
                else {
                    // Все следующие сообщения этого автора после текущего прочитанного
                    $messagesAfterCurrent = $this->getMessagesAfterCurrent($designChat, $message);

                    $boolNextJob = false;
                    // 2,1 Проверяем каждое сообщение после текущего
                    foreach ($messagesAfterCurrent as $nextMessage) {
                        // Проверяем, прочитано ли сообщение всеми пользователями
                        $allNotifiedRead = $this->checkAllNotifiedRead($nextMessage);

                        // Повторно ставим задачу с задержкой на 1 мин для нового $messageId
                        if (!$allNotifiedRead) {
                            $this->messageId = $nextMessage['id'];
                            $this->dispatchJobForNextMessage($this->messageId, 1);
                            $boolNextJob = true;
                            break;
                        }
                    }

                    // 2,2 ЗАВЕРШЕНИЕ цепочки Job
                    // Удаляем ID автора из массива job_timer_user_ids поставленных задач
                    if(!$boolNextJob){
                        $this->removeAuthorFromTimerUserIds($designChat);
                    }
                }
            }
        }
    }

    /**
     * Проверка, прочитано ли сообщение всеми пользователями
     *
     * @param $message
     * @return bool
     */
    private function checkAllNotifiedRead($message): bool {
        return collect($this->notifyUserIds)->diff($message['who_read_messages'])->isEmpty();
    }

    /**
     * Получение всех сообщений автора после текущего
     *
     * @param $designChat
     * @param $message
     * @return Collection
     */
    private function getMessagesAfterCurrent($designChat, $message): Collection {
        return collect($designChat->messages)->filter(function ($msg) use ($message) {
            return $msg['sender_id'] == $this->authorId && $msg['id'] > $this->messageId;
        });
    }

    /**
     * Удаление автора из массива job_timer_user_ids
     *
     * @param $designChat
     * @return void
     */
    private function removeAuthorFromTimerUserIds($designChat): void {
        $user_ids = $designChat->job_timer_user_ids;

        if (is_array($user_ids) && in_array($this->authorId, $user_ids)) {
            // Исключения ID автора
            $user_ids = collect($user_ids)
                ->reject(function($id) {
                    return $id == $this->authorId;  // Идентификатор автора
                })
                ->values();  // Пересчитываем индексы после удаления элемента

            // Сохраняем обновленный список пользователей
            $designChat->job_timer_user_ids = $user_ids;
            $designChat->save();
        }
    }

    /**
     * Установка задачи
     * @param $messageId
     * @param $timer
     * @return void
     */
    private function dispatchJobForNextMessage($messageId, $timer): void {
        dispatch(new OneMinuteCheckUnreadMessagesJob(
            $this->authorId,       // Автор сообщения
            $this->notifyUserIds,  // Собеседники
            $this->chatId,         // Чат ID
            $messageId,            // В котором нет id участников
            $timer
        ))->delay(now()->addMinutes($timer));
    }
}

