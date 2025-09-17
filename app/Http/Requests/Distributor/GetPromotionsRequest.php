<?php

namespace App\Http\Requests\Distributor;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class GetPromotionsRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'count_show' => 'required|integer|min:10',     // по сколько показывать записей в таблице
            'start_index' => 'required|integer|min:0',     // номер страницы пагинации
            'obj_search.field' => 'nullable|string',       // название столбца в которой поиск (search)
            'obj_search.input_value' => 'nullable|string', // значение input в поиске по таблице (search)
            'obj_search.only_active' => 'required|boolean',// значение checkbox (фильтр в таблице)
            'obj_search.date_picker' => 'nullable|array',  // значение date picker (фильтр в таблице)
            'obj_search.date_picker.from' => 'nullable|date',  // дата начала
            'obj_search.date_picker.to' => 'nullable|date',    // дата окончания
            'sort_by' => 'nullable|string',                // название столбца таблицы (клик по сортировке)
            'sort_count' => 'required|integer|min:0',      // количество нажатий по столбцу сортировки таблицы (клик по сортировке)
        ];
    }

    // обработка полей перед валидацией
    protected function prepareForValidation() {

        // 1 Установить bool
        $only_active = $this->input('obj_search.only_active');
        if (is_string($only_active)) {
            $only_active = filter_var($only_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        // 2 Проверка поля date_picker
        $date_picker = $this->input('obj_search.date_picker');

        if (
            is_array($date_picker) &&
            isset($date_picker['from']) &&
            isset($date_picker['to']) &&
            !empty($date_picker['from']) &&
            !empty($date_picker['to'])
        ) {
            $fromDate = $date_picker['from'];
            $toDate = $date_picker['to'];

            try {
                $from = Carbon::parse($fromDate);
                $to = Carbon::parse($toDate);

                // Проверяем, что даты валидны и start <= end
                if (!$from || !$to || !$from->lte($to)) {
                    $date_picker = null;
                }
            }
            catch (\Exception $e) {
                $date_picker = null;
            }
        }
        else {
            $date_picker = null;
        }

        $this->merge([
            'obj_search' => [
                'field' => $this->input('obj_search.field'),
                'input_value' => $this->input('obj_search.input_value'),
                'date_picker' => $date_picker,
                'only_active' => $only_active,
            ],
            'sort_by' => $this->input('sort_by'),
        ]);
    }
}
