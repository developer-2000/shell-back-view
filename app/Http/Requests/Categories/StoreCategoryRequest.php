<?php

namespace App\Http\Requests\Categories;

use App\Enums\CategoryGroup;
use App\Models\Test;
use Illuminate\Foundation\Http\FormRequest;
use \App\Models\User;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    /**
     * Validation используется как при создании так и при обновлении category
     * @return string[]
     */
    public function rules() {
        $categoryId = $this->route('category') ? $this->route('category')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($categoryId),
            ],
            'manager_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists(User::class, 'id'),
            ],
            'groups' => [
                'sometimes',
                'array',
            ],
            'groups.*' => [
                'nullable',
                'string',
                Rule::enum(CategoryGroup::class),
            ],
            'required' => 'required|boolean'
        ];
    }

    /**
     * Обработка данных перед валидацией.
     */
    protected function prepareForValidation() {
        $required = $this->input('required');
        if (is_string($required)) {
            $required = filter_var($required, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $this->merge([
            'groups' => $this->input('groups', []),
            'required' => $required,
            'manager_id' => $this->input('manager_id', null),
        ]);
    }
}
