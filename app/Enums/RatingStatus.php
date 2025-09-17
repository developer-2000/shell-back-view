<?php

declare(strict_types=1);

namespace App\Enums;

enum RatingStatus: string {
    case CANCELLED = 'Cancelled';
    case CORRECTION = 'Correction';
    case APPROVED = 'Approved';
}
