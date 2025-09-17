<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendAboutNewPromotionMail extends Mailable {
    use Queueable, SerializesModels;

    public $promotionId;

    public function __construct($promotionId) {
        $this->promotionId = $promotionId;
    }

    public function build() {
        $link = url("https://new.st1shop.no/promotion-settings?prom_id={$this->promotionId}&action=surfaces");
        return $this->view('emails.new_promotion')
            ->with(['link' => $link])
            ->subject('New Promotion Created');
    }
}
