<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteUserRequest extends FormRequest {
    public function authorize() {
        return true;
    }

    public function rules() {
        return []; // Пустое правило, так как валидация не требуется
    }

    // Метод для получения ID пользователя из маршрута
    public function userId() {
        return $this->route('user'); // Здесь 'user' - это имя параметра маршрута
    }

}
