<?php

namespace App\Jobs;

use App\Mail\SentUserAboutTrackerDistributorMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SentUserAboutTrackerDistributorJob implements ShouldQueue {
    use Queueable, SerializesModels;

    public $user;
    public $promotionName;
    public $tracker;
    public $surfaceNames;

    public function __construct(User $user, $promotionName, $tracker, array $surfaceNames) {
        $this->user = $user;
        $this->promotionName = $promotionName;
        $this->tracker = $tracker;
        $this->surfaceNames = $surfaceNames;
    }

    public function handle() {
        Mail::to($this->user->email)->send(new SentUserAboutTrackerDistributorMail(
            $this->user->name,
            $this->promotionName,
            $this->tracker,
            $this->surfaceNames
        ));
    }
}
