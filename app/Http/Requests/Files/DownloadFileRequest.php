<?php

namespace App\Http\Requests\Files;

use App\Rules\AllowedImageExtensionsRule;
use Illuminate\Foundation\Http\FormRequest;

class DownloadFileRequest extends FormRequest {

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
        $tableName = $this->input('table_name');

        return [
            // Проверка, что table_name существует и является строкой
            'table_name' => ['required', 'string'],
            'table_id' => ['required', 'integer'],
            'message_id' => ['required', 'integer'],
        ];
    }
}
