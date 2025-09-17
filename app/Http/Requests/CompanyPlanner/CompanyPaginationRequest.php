<?php

namespace App\Http\Requests\CompanyPlanner;

use Illuminate\Foundation\Http\FormRequest;

class CompanyPaginationRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'count_show' => 'required|integer|min:10',     // по сколько показывать записей в таблице
            'start_index' => 'required|integer|min:0',     // номер страницы пагинации
            'sort_by' => 'nullable|string',                // название столбца таблицы (клик по сортировке)
            'sort_count' => 'required|integer|min:0',      // количество нажатий по столбцу сортировки таблицы (клик по сортировке)
            'obj_search.field' => 'nullable|string',       // название столбца в которой поиск (search)
            'obj_search.input_value' => 'nullable|string', // значение input в поиске по таблице (search)
            'obj_search.only_quantity' => 'required|boolean',
        ];
    }

    // обработка полей перед валидацией
    protected function prepareForValidation() {
        $only_quantity = $this->input('obj_search.only_quantity');
        if (is_string($only_quantity)) {
            $only_quantity = filter_var($only_quantity, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $this->merge([
            'obj_search' => [
                'field' => $this->input('obj_search.field'),
                'input_value' => $this->input('obj_search.input_value'),
                'only_quantity' => $only_quantity,
            ],
            'sort_by' => $this->input('sort_by')
        ]);
    }
}
