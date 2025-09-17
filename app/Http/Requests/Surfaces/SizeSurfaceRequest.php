<?php

namespace App\Http\Requests\Surfaces;

use Illuminate\Foundation\Http\FormRequest;

class SizeSurfaceRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'new_size' => 'required|string|max:255|unique:size_surfaces,title',
        ];
    }

}
