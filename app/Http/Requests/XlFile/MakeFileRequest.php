<?php

namespace App\Http\Requests\XlFile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MakeFileRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'promotion_id' => 'required|integer|exists:promotion_surface_designs,promotion_id',
            'promotion_name' => 'required|string|min:2|max:255',
            'display_address' => 'sometimes|nullable|boolean',
            'display_categories' => 'sometimes|nullable|boolean',
            'number_percent' => 'required|integer',
        ];
    }

    /**
     * Обработка данных перед валидацией.
     */
    protected function prepareForValidation() {
        $display_address = $this->input('display_address');
        if (is_string($display_address)) {
            $display_address = filter_var($display_address, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $display_categories = $this->input('display_categories');
        if (is_string($display_categories)) {
            $display_categories = filter_var($display_categories, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $this->merge([
            'display_address' => $display_address,
            'display_categories' => $display_categories,
        ]);
    }

}
