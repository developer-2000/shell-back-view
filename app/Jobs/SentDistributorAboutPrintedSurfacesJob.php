<?php

namespace App\Jobs;

use App\Mail\SentDistributorAboutPrintedSurfacesMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SentDistributorAboutPrintedSurfacesJob implements ShouldQueue {
    use Queueable, SerializesModels;

    protected $distributor;
    protected $validated;
    protected $printerId;

    public function __construct(User $distributor, array $validated, int $printerId) {
        $this->distributor = $distributor;
        $this->validated = $validated;
        $this->printerId = $printerId;
    }

    public function handle() {
        Mail::to($this->distributor->email)
            ->send(new SentDistributorAboutPrintedSurfacesMail(
                $this->distributor->name,
                $this->validated,
                $this->printerId
            ));
    }
}
