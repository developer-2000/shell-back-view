<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SentUserAboutTrackerDistributorMail extends Mailable {
    use Queueable, SerializesModels;

    public $companyName;
    public $promotionName;
    public $tracker;
    public $surfaceNames;

    public function __construct($companyName, $promotionName, $tracker, array $surfaceNames) {
        $this->companyName = $companyName;
        $this->promotionName = $promotionName;
        $this->tracker = $tracker;
        $this->surfaceNames = implode(', ', $surfaceNames);
    }

    public function build() {
        $trackingLink = "https://sporing.posten.no/sporing/{$this->tracker}";

        return $this
            ->subject('Your Order Has Been Shipped')
            ->view('emails.sent_user_about_tracker_distributor')
            ->with([
                'companyName' => $this->companyName,
                'promotionName' => $this->promotionName,
                'trackingLink' => $trackingLink,
                'surfaceNames' => $this->surfaceNames
            ]);
    }
}
