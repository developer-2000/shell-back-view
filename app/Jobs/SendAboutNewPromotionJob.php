<?php

namespace App\Jobs;

use App\Mail\SendAboutNewPromotionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAboutNewPromotionJob implements ShouldQueue {
    use Queueable, SerializesModels;

    protected $email;
    protected $promotionId;

    public function __construct($email, int $promotionId) {
        $this->email = $email;
        $this->promotionId = $promotionId;
    }

    public function handle() {
        Mail::to($this->email)->send(new SendAboutNewPromotionMail($this->promotionId));
    }
}
