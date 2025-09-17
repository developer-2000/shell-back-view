<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendAboutNewDesignMail extends Mailable {
    use Queueable, SerializesModels;

    public $promotionLink;
    public $designLink;

    public function __construct($promotionLink, $designLink) {
        $this->promotionLink = $promotionLink;
        $this->designLink = $designLink;
    }

    public function build() {
        return $this->view('emails.new_design')
            ->with([
                'promotionLink' => $this->promotionLink,
                'designLink' => $this->designLink
            ])
            ->subject('New Design Created for Promotion');
    }
}
