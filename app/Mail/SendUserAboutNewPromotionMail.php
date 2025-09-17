<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendUserAboutNewPromotionMail extends Mailable {
    use Queueable, SerializesModels;

    public $userData;
    public $promotionId;

    // Принимаем данные пользователя и ID промоакции
    public function __construct($userData, $promotionId) {
        $this->userData = $userData;
        $this->promotionId = $promotionId;
    }

    public function build() {
        $link = url("https://new.st1shop.no/user-promotions");

        // Проходим по всем данным пользователя и собираем свойства
        $properties = [];
        foreach ($this->userData as $key => $value) {
            if ($key != 'user_name' && $key != 'user_email') {
                $properties[$key] = $value;
            }
        }

        // Строим письмо
        return $this->view('emails.new_promotion_user')
            ->with([
                'userName' => $this->userData['user_name'],
                'promotionId' => $this->promotionId,
                'link' => $link,
                'properties' => $properties
            ])
            ->subject('New Promotion Created');
    }
}
