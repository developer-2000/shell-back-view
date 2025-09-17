<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendToPrinterAboutSurfaceStatus extends Mailable {
    use Queueable, SerializesModels;

    public $printerName;
    public $promotionId;

    public function __construct($printerName, $promotionId) {
        $this->printerName = $printerName;
        $this->promotionId = $promotionId;
    }

    public function build() {
        $link = url("https://new.st1shop.no/print-promotion-report?prom_id={$this->promotionId}");

        return $this
            ->subject('Promotion Designs Completed')
            ->view('emails.printer_surface_status')
            ->with(['printerName' => $this->printerName, 'promotionLink' => $link,]);
    }
}
