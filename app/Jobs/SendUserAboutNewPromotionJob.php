<?php

namespace App\Jobs;

use App\Mail\SendAboutNewPromotionMail;
use App\Mail\SendUserAboutNewPromotionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserAboutNewPromotionJob implements ShouldQueue {
    use Queueable, SerializesModels;

    protected $userData;
    protected $promotionId;

    // Принять данные о пользователе и ID промоакции
    public function __construct($userData, int $promotionId) {
        $this->userData = $userData;
        $this->promotionId = $promotionId;
    }

    public function handle() {
        // Отправляем письмо для каждого пользователя
        Mail::to($this->userData['user_email'])->send(
            new SendUserAboutNewPromotionMail($this->userData, $this->promotionId)
        );
    }
}
