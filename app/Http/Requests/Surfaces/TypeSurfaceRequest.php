<?php

namespace App\Http\Requests\Surfaces;

use Illuminate\Foundation\Http\FormRequest;

class TypeSurfaceRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'new_type' => 'required|string|max:255|unique:type_surfaces,title',
        ];
    }

}
