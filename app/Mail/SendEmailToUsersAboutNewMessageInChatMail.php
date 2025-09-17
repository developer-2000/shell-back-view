<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailToUsersAboutNewMessageInChatMail extends Mailable {
    use Queueable, SerializesModels;

    public $titleChat;
    public $link;

    /**
     * Создаем экземпляр класса с переданными данными.
     */
    public function __construct(string $titleChat, string $link) {
        $this->titleChat = $titleChat;
        $this->link = $link;
    }

    /**
     * Формируем письмо.
     */
    public function build() {
        return $this
            ->subject('New Message in Design Chat')
            ->view('emails.send_email_to_user_about_new_message_in_chat')
            ->with(['titleChat' => $this->titleChat, 'link' => $this->link]);
    }
}
