<?php

namespace App\Http\Requests\Surfaces;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SurfaceCloneRequest extends FormRequest {

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
        $id = $this->input('surface_id');

        $rules = [
            'surface_id' => 'required|exists:surfaces,id',
            'name' => 'required|string|max:255|unique:surfaces,name,' . $id,
            'bool_clone' => 'required|boolean',
        ];

        return $rules;
    }

    /**
     * Обработка данных перед валидацией.
     */
    protected function prepareForValidation() {
        $bool_clone = $this->input('bool_clone');
        if (is_string($bool_clone)) {
            $bool_clone = filter_var($bool_clone, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $this->merge([
            'bool_clone' => $bool_clone,
        ]);
    }

}
