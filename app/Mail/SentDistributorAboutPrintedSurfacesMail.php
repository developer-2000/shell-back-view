<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SentDistributorAboutPrintedSurfacesMail extends Mailable {
    use Queueable, SerializesModels;

    public $distributorName;
    public $validated;
    public $printerId;

    public function __construct($distributorName, $validated, $printerId) {
        $this->distributorName = $distributorName;
        $this->validated = $validated;
        $this->printerId = $printerId;
    }

    public function build() {
        $link = url("https://new.st1shop.no/distributor-promotion-view?prom_id={$this->validated["promotion_id"]}&printer_id={$this->printerId}");
        $trackingLink = "https://sporing.posten.no/sporing/{$this->validated["printer_tracker_number"]}";

        return $this
            ->subject('Printed surfaces promotion')
            ->view('emails.sent_distributor_about_printed_surfaces')
            ->with([
                'distributorName' => $this->distributorName,
                'promotionLink' => $link,
                'trackingLink' => $trackingLink,
                ]);
    }
}
