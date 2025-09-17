<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WhoReadEvent implements ShouldBroadcastNow  {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $message;
    public int $userId;

    /**
     * @param array $message
     * @param int $userId
     */
    public function __construct(array $message, int $userId) {
        $this->message = $message;
        $this->userId = $userId;
    }

    public function broadcastOn(): Channel {
        return new Channel('chat_user_' . $this->userId);
    }

}
