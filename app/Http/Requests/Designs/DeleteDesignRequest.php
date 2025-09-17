<?php

namespace App\Http\Requests\Designs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteDesignRequest extends FormRequest {
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'design_id' => [
                'required',
                'integer',
                Rule::exists('designs', 'id'),
            ],
        ];
    }

}
