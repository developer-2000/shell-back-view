<?php

namespace App\Jobs;

use App\Mail\SendAboutNewDesignMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAboutNewDesignJob implements ShouldQueue {
    use Queueable, SerializesModels;

    protected $email;
    protected $promotionLink;
    protected $designLink;

    public function __construct($email, $promotionLink, $designLink) {
        $this->email = $email;
        $this->promotionLink = $promotionLink;
        $this->designLink = $designLink;
    }

    public function handle() {
        Mail::to($this->email)->send(new SendAboutNewDesignMail($this->promotionLink, $this->designLink));
    }
}
