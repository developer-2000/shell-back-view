<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => [
                'required',
                'string',
                'min:8', // Минимум 8 символов
                // Латинские буквы (a-z, A-Z)
                // Цифры (0-9)
                // Безопасные символы: @, #, %, ^, &, *, (), _, +, =, -, [], {}, ;, :, ,, ., ?, !
                'regex:/^[a-zA-Z0-9@#%^&*()_+=\-\[\]{};:,.?!]+$/',
            ],
        ];
    }

}
