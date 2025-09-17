<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAboutDeletedDesignMail extends Mailable {
    use Queueable, SerializesModels;

    public $userName;
    public $promotionName;
    public $surfaceName;
    public $designName;
    public $promotionLink;

    public function __construct($userName, $promotionName, $surfaceName, $designName, $promotionLink) {
        $this->userName = $userName;
        $this->promotionName = $promotionName;
        $this->surfaceName = $surfaceName;
        $this->designName = $designName;
        $this->promotionLink = $promotionLink;
    }

    public function build() {
        return $this->view('emails.deleted_design')
            ->with([
                'userName' => $this->userName,
                'promotionName' => $this->promotionName,
                'surfaceName' => $this->surfaceName,
                'designName' => $this->designName,
                'promotionLink' => $this->promotionLink,
            ])
            ->subject('Design Removed from Promotion');
    }
}
