<?php

namespace App\Http\Requests\DesignChat;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CreateMessageRequest extends FormRequest {

    /**
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array {
        $rules = [
            'chat_id' => 'nullable|integer|exists:design_chats,id',
            'message_id' => 'nullable|integer',
            'text' => 'nullable|string',
            'url_file' => 'nullable|string',
            'file_name' => 'nullable|string',
            'file_size' => 'nullable|string',
            'type_extension' => 'nullable|string',
            'type_file' => 'nullable|string',
            'comment_mess_id' => 'nullable|integer',
            'update_message' => 'required|boolean',
            'make_size' => 'required|array',
        ];

        return $rules;
    }

    /**
     * Обработка данных перед валидацией.
     */
    protected function prepareForValidation() {
        $updateMessage = $this->input('update_message', null);
        if (is_string($updateMessage)) {
            $updateMessage = filter_var($updateMessage, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }
        if (is_null($updateMessage)) {
            $updateMessage = false;
        }

        $this->merge([
            'update_message' => $updateMessage,
        ]);
    }

}
