<?php

namespace App\Jobs;

use App\Mail\SendAboutDeletedDesignMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAboutDeletedDesignJob implements ShouldQueue {
    use Queueable, SerializesModels;

    protected $email;
    protected $userName;
    protected $promotionName;
    protected $surfaceName;
    protected $designName;
    protected $promotionLink;

    public function __construct($email, $userName, $promotionName, $surfaceName, $designName, $promotionLink) {
        $this->email = $email;
        $this->userName = $userName;
        $this->promotionName = $promotionName;
        $this->surfaceName = $surfaceName;
        $this->designName = $designName;
        $this->promotionLink = $promotionLink;
    }

    public function handle() {
        Mail::to($this->email)->send(new SendAboutDeletedDesignMail(
            $this->userName,
            $this->promotionName,
            $this->surfaceName,
            $this->designName,
            $this->promotionLink
        ));
    }
}
