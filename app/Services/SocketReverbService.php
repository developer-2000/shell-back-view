<?php

namespace App\Services;

use App\Models\User;


class SocketReverbService {

    protected User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    // Отправка сообщений всем участникам чата.
    public function sendingMessagesToParticipants(array $usersIds, array $data, string $eventClass): array {
        // Удаляем из массива ID текущего пользователя
        $idArr = array_diff($usersIds, [$this->user->id]);

        // Проходим по каждому id участника и отправляем сообщение
        foreach ($idArr as $userId) {
            try {
                broadcast(new $eventClass($data, $userId));
            } catch (\Exception $e) {
                return ['success' => false, 'status_code' => 500, 'message' => "Error sending message to user {$userId}: " . $e->getMessage()];
            }
        }

        return ['success' => true,];
    }

}


