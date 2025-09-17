<?php

namespace App\Http\Requests\Distributor;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class SetDistributorTrackerDataRequest extends FormRequest {

    /**
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array {
        return [
            'promotion_id' => 'required|integer|exists:promotions,id',
            'company_id' => 'required|integer|exists:users,id',
            // Убираем все пробелы и проверяем на хотя бы один символ
            'tracker_number' => ['required', 'string', 'regex:/\S+/'],
            'sent_surfaces' => 'required|array',
            'sent_surfaces.*' => 'array',
            'sent_surfaces.*.name' => 'required|string',
            'sent_surfaces.*.surface_id' => 'required|integer',
            'sent_surfaces.*.design_id' => 'required|integer',
            'sent_surfaces.*.amount' => 'required|integer',
            'description' => 'nullable|string'
        ];
    }

    /**
     * Обработка данных перед валидацией.
     */
    protected function prepareForValidation() {
        $this->merge([
            'description' => $this->input('description', null),
        ]);
    }

}
