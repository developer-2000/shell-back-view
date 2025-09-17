<?php

namespace App\Http\Requests\Files;

use App\Rules\AllowedImageExtensionsRule;
use Illuminate\Foundation\Http\FormRequest;

class SetImageRequest extends FormRequest {

    /**
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array {
        $rules = [
            'img_url' => [
                'required',
                'string',
                'regex:/^data:image\/(jpg|jpeg|png|tiff);base64,/', // Добавляем проверку на формат
                new AllowedImageExtensionsRule(),
            ],
            'img_name' => 'required|string',
            'make_size' => 'required|array',
        ];

        return $rules;
    }
}
