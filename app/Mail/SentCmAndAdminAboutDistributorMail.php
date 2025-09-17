<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SentCmAndAdminAboutDistributorMail extends Mailable {
    use Queueable, SerializesModels;

    public $user;
    public $promotionName;
    public $userNames;

    public function __construct($user, $promotionName, $userNames) {
        $this->user = $user;
        $this->promotionName = $promotionName;
        $this->userNames = $userNames;
    }

    public function build() {
        return $this->subject('Distributor Tracker Numbers Update')
            ->view('emails.cm_and_admin_about_distributor')
            ->with([
                'userName' => $this->user->name,
                'promotionName' => $this->promotionName,
                'userNames' => $this->userNames
                ]);
    }
}
