<?php

namespace App\Http\Requests\Surfaces;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SurfaceSaveRequest extends FormRequest {

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
            'vendor_code' => 'required|integer',
            'price' => 'required|numeric',
            'name' => 'required|string|max:255|unique:surfaces,name',
            'type_surface' => 'nullable|string|max:255|exists:type_surfaces,title',
            'size_surface' => 'nullable|string|max:255|exists:size_surfaces,title',
            'description' => 'nullable|string',
            'divided_bool' => 'required|boolean',
            'status' => 'array',
            'status.*' => [ 'nullable', 'integer' ],
            'printer_id' => 'nullable|integer|exists:users,id'
        ];

        return $rules;
    }

    /**
     * Обработка данных перед валидацией.
     */
    protected function prepareForValidation() {
        $divided_bool = $this->input('divided_bool');
        if (is_string($divided_bool)) {
            $divided_bool = filter_var($divided_bool, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $this->merge([
            'divided_bool' => $divided_bool,
            'status' => $this->input('status', []),
            'printer_id' => $this->input('printer_id', null),
        ]);
    }

}
