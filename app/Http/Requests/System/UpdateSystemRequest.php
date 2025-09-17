<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules() {
        $rules = [
            'id' => 'nullable|integer|exists:system_settings,id',
            'distributor_id' => 'nullable|integer|exists:users,id',
            'admin_id' => 'nullable|integer|exists:users,id',
            'percent_promotion_report' => 'required|integer',
        ];

        return $rules;
    }

    /**
     * Обработка данных перед валидацией.
     */
    protected function prepareForValidation() {
        $this->merge([
            'id' => $this->input('id', null),
            'distributor_id' => $this->input('distributor_id', null),
            'admin_id' => $this->input('admin_id', null),
        ]);
    }

}
