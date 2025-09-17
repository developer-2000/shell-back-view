<?php

namespace App\Jobs;

use App\Mail\SendToPrinterAboutSurfaceStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPromotionEmailsToPrinters implements ShouldQueue {
    use Queueable, SerializesModels;

    protected $printers;
    protected $promotionId;

    public function __construct(array $printers, int $promotionId) {
        $this->printers = $printers;
        $this->promotionId = $promotionId;
    }

    public function handle() {
        foreach ($this->printers as $printer) {
            Mail::to($printer['email'])
                ->send(new SendToPrinterAboutSurfaceStatus($printer['name'], $this->promotionId));
        }
    }
}
