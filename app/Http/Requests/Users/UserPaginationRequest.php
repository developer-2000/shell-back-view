<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserPaginationRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'count_show' => 'required|integer|min:10',     // по сколько показывать записей в таблице
            'obj_search.admin_only' => 'required|boolean', // показ только админов (checkbox)
            'obj_search.field' => 'nullable|string',       // название столбца в которой поиск (search)
            'obj_search.input_value' => 'nullable|string', // значение input в поиске по таблице (search)
            'sort_by' => 'nullable|string',                // название столбца таблицы (клик по сортировке)
            'sort_count' => 'required|integer|min:0',      // количество нажатий по столбцу сортировки таблицы (клик по сортировке)
            'start_index' => 'required|integer|min:0',     // номер страницы пагинации
        ];
    }

    // обработка полей перед валидацией
    protected function prepareForValidation() {
        $adminOnly = $this->input('obj_search.admin_only');
        if (is_string($adminOnly)) {
            $adminOnly = filter_var($adminOnly, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $this->merge([
            'obj_search' => [
                'field' => $this->input('obj_search.field'),
                'input_value' => $this->input('obj_search.input_value'),
                'admin_only' => $adminOnly,
            ],
            'sort_by' => $this->input('sort_by')
        ]);
    }
}
