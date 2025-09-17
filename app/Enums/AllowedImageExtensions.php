<?php

declare(strict_types=1);

namespace App\Enums;

enum AllowedImageExtensions: string {
    case JPG = 'jpg';
    case JPEG = 'jpeg';
    case PNG = 'png';
    case TIFF = 'tiff';

    public static function values(): array {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
