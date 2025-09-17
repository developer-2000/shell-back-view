<?php

namespace App\Http\Requests\Surfaces;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteSurfaceRequest extends FormRequest {
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'surface_id' => [
                'required',
                'integer',
                Rule::exists('surfaces', 'id'),
            ],
        ];
    }

}
