<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Enums\AllowedImageExtensions;

class AllowedImageExtensionsRule implements Rule {
    public function passes($attribute, $value): bool {
        // Проверка на тип файла
        if (preg_match('/^data:image\/(\w+);base64,/', $value, $type)) {
            $extension = strtolower($type[1]);
            return in_array($extension, AllowedImageExtensions::values());
        }

        return false;
    }

    public function message(): string {
        return 'The :attribute must be a valid image of type: ' . implode(', ', AllowedImageExtensions::values()) . '.';
    }
}
