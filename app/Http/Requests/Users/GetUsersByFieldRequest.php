<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class GetUsersByFieldRequest extends FormRequest {
    public function authorize() {
        return true;
    }

    /**
     * Правила валидации для запроса.
     *
     * @return array
     */
    public function rules() {
        return [
            'field' => 'required|string',
            'value' => 'required|string'
        ];
    }
}
