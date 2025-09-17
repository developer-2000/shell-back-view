<?php

namespace App\Http\Requests\Users;

use App\Enums\CategoryGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserSaveRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    /**
     * Validation используется как при создании так и при обновлении user
     * @return string[]
     */
    public function rules() {
        $rules = [
            'email' => 'required|email',
            'name' => 'required|string',
            'role' => 'required|string',
            'group' => [
                'required',
                'string',
                Rule::enum(CategoryGroup::class),
            ],
            'surname' => 'nullable|string',
            'name_invoice_recipient' => 'nullable|string',
            'email_invoice_recipient' => 'nullable|string',
            'company_name' => 'nullable|string',
            'company_number' => 'nullable|string',
            'c_o' => 'nullable|string',
            'post_address' => 'nullable|string',
            'postcode' => 'nullable|string',
            'phone' => 'nullable|string',
            'phone_2' => 'nullable|string',
            'municipality_number' => 'nullable|string',
            'kommune' => 'nullable|string',
            'country' => 'nullable|string',
            'number_country' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'status' => 'required|boolean',
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'nullable|integer|exists:categories,id',
        ];

        // Пароль передан при создании user
        if (!empty($this->input('password'))) {
            // Латинские буквы (a-z, A-Z) Цифры (0-9)
            // Безопасные символы: @, #, %, ^, &, *, (), _, +, =, -, [], {}, ;, :, ,, ., ?, !
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'regex:/^[a-zA-Z0-9@#%^&*()_+=\-\[\]{};:,.?!]+$/',
            ];
            $rules['email'] .= '|unique:users,email';
        }
        else {
            $rules['password'] = 'nullable|string';
        }

        return $rules;
    }

    /**
     * Обработка данных перед валидацией.
     */
    protected function prepareForValidation() {
        $status = $this->input('status');
        if (is_string($status)) {
            $status = filter_var($status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $this->merge([
            'surname' => $this->input('surname', ''),
            'name_invoice_recipient' => $this->input('name_invoice_recipient', ''),
            'email_invoice_recipient' => $this->input('email_invoice_recipient', ''),
            'company_name' => $this->input('company_name', ''),
            'company_number' => $this->input('company_number', ''),
            'c_o' => $this->input('c_o', ''),
            'post_address' => $this->input('post_address', ''),
            'postcode' => $this->input('postcode', ''),
            'phone' => $this->input('phone', ''),
            'phone_2' => $this->input('phone_2', ''),
            'municipality_number' => $this->input('municipality_number', ''),
            'kommune' => $this->input('kommune', ''),
            'country' => $this->input('country', ''),
            'number_country' => $this->input('number_country', ''),
            'reference_number' => $this->input('reference_number', ''),
            'status' => $status,
            'category_ids' => $this->input('category_ids', []),
        ]);
    }
}
