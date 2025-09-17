<?php

namespace App\Repositories;

use App\Models\FeedbackMessage;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;

class FeedbackRepository extends BaseRepository {

    /**
     * Создание promotion
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом создания.
     */
    public function createFeedback(array $validated, User $currentUser): array {
        try {
            // Создаем новый объект сообщения
            $newMessage = [
                "id" => 1,
                'text' => $validated['message'] ?? '',
                "sender_id" => $currentUser->id,
                'who_read_messages' => [$currentUser->id],
                "create_date" => Carbon::now(),
            ];

            $feedback = FeedbackMessage::create([
                "title" => $validated['title'],
                "from_user_id" => $currentUser->id,
                "to_user_id" => $validated['to_user_id'],
                "messages" => [$newMessage],
            ]);

            if ($feedback) {
                return [
                    'success' => true,
                    'message' => 'Feedback saved successfully!',
                    'status_code' => 201,
                ];
            }
            else {
                return [
                    'success' => false,
                    'message' => 'Failed to create feedback.',
                    'status_code' => 500
                ];
            }
        }
        catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Добавить новый message в Feedback
     *
     * @param array $validated
     * @param User $currentUser
     * @return array
     */
    public function addMessage(array $validated, User $currentUser): array {
        try {
            // Находим нужный FeedbackMessage
            $feedback = FeedbackMessage::find($validated['feedback_id']);
            $messages = collect($feedback->messages);
            // Получаем максимальный id
            $maxMessageId = $messages->max('id');

            // Создаем новый объект сообщения
            $newMessage = [
                "id" => $maxMessageId + 1,
                'text' => $validated['message'] ?? '',
                "sender_id" => $currentUser->id,
                'who_read_messages' => [$currentUser->id],
                "create_date" => Carbon::now(),
            ];

            // Добавляем новое сообщение в коллекцию
            $messages->push($newMessage);
            $feedback->messages = $messages->toArray();

            if (!$feedback->save()) {
                return [
                    'success' => false,
                    'message' => 'Failed to add message.',
                    'status_code' => 500
                ];
            }

            // 2 его участники
            $users = $this->getUsersFeedback($feedback);

            // Если нет участников, возвращаем ошибку
            if (empty($users)) {
                return [
                    'success' => false,
                    'message' => 'No users found for this feedback.',
                    'data' => [],
                    'status_code' => 404,
                ];
            }

            return [
                'success' => true,
                'message' => 'Message added successfully',
                'data' => compact("feedback", "users"),
                'status_code' => 201,
            ];
        }
        catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function getFeedbackData(array $validatedData, User $currentUser): array {
        // 1 feedback
        $feedback = FeedbackMessage::find($validatedData['feedback_id'])->first();
        // 2 его участники
        $users = $this->getUsersFeedback($feedback);

        // Если нет участников, возвращаем ошибку
        if (empty($users)) {
            return [
                'success' => false,
                'message' => 'No users found for this feedback.',
                'data' => [],
                'status_code' => 404,
            ];
        }

        // 3 Прочесть все сообщения если они не мои
        $messages = $feedback->messages; // Копируем сообщения в обычный массив
        $messagesChanged = false; // Переменная для отслеживания изменений

        // 4 Прочесть все сообщения, если они не мои
        foreach ($messages as &$message) {
            // Если сообщение не мое
            if ($message['sender_id'] !== $currentUser->id) {
                // Если в массиве прочитанных юзеров меня нет
                if (!in_array($currentUser->id, $message['who_read_messages'])) {
                    $message['who_read_messages'][] = $currentUser->id;
                    $messagesChanged = true;
                }
            }
        }

        // Если изменения были, сохраняем их
        if ($messagesChanged) {
            $feedback->messages = $messages;
            $feedback->save();
        }

        return [
            'success' => true,
            'message' => '',
            'data' => compact("feedback", "users"),
            'status_code' => 201,
        ];
    }

    /**
     * Users feedback
     *
     * @param FeedbackMessage $feedback
     * @return array
     */
    private function getUsersFeedback(FeedbackMessage $feedback): array {
        $uniqueIds = array_unique(
            Arr::flatten(Arr::pluck($feedback->messages, 'who_read_messages'))
        );

        return User::whereIn('id', $uniqueIds)->get()->toArray();
    }

}
