<?php

namespace App\Jobs;

use App\Mail\SentCmAndAdminAboutDistributorMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SentCmAndAdminAboutDistributorJob implements ShouldQueue {
    use Queueable, SerializesModels;

    public $users;
    public $promotionName;
    public $userNames;

    public function __construct($users, $promotionName, $userNames) {
        $this->users = $users;
        $this->promotionName = $promotionName;
        $this->userNames = $userNames;
    }

    public function handle() {
        foreach ($this->users as $user) {
            Mail::to($user->email)
                ->send(new SentCmAndAdminAboutDistributorMail($user, $this->promotionName, $this->userNames));
        }
    }
}
