<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class GetUsersIdsRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'users_ids' => 'nullable|array',
            'users_ids.*' => 'integer',
        ];
    }

    // обработка полей перед валидацией
    protected function prepareForValidation() {
        $this->merge([
            'users_ids' => $this->input('users_ids', []),
        ]);
    }
}
