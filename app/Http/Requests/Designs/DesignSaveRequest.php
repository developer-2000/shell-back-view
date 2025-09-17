<?php

namespace App\Http\Requests\Designs;

use Illuminate\Foundation\Http\FormRequest;

class DesignSaveRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    /**
     * Validation используется как при создании так и при обновлении
     * @return string[]
     */
    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|max:255|exists:categories,id',
        ];
    }

}
