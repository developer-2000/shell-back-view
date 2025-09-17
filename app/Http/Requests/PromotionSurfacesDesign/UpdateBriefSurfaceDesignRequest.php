<?php

namespace App\Http\Requests\PromotionSurfacesDesign;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBriefSurfaceDesignRequest extends FormRequest {

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
        // Получаем данные из конфигурации
        $promotionConfig = config('site.promotion_surface_design');

        return [
            'additional_description' => 'nullable|string',
            'color' => 'required|string|in:' . implode(',', $promotionConfig['color_options']),
            'description' => 'nullable|string',
            'ean_more' => 'nullable|string',

            'need_for_price' => 'required|array',
            'need_for_price.title' => 'required|string',
            'need_for_price.value' => 'required|boolean',
            'not_for_printing' => 'required|array',
            'not_for_printing.title' => 'required|string',
            'not_for_printing.value' => 'required|boolean',

            'plu_scan' => 'required|string|in:' . implode(',', $promotionConfig['plu_scan']),
            'promotional_offer' => 'required|integer|min:0',
            'status' => 'required|string|in:' . implode(',', $promotionConfig['brief_status']),
            'sub_title' => 'nullable|string',
            'supplier_discount' => 'required|string|regex:/^\d{1,2}%$/', // Процентное значение от 0% до 99%
            'text_italic' => 'nullable|string',
            'title' => 'nullable|string',
        ];
    }

    protected function prepareForValidation() {
        // Преобразуем need_for_price.value в boolean
        $needForPriceValue = $this->input('need_for_price.value');
        if (is_string($needForPriceValue)) {
            $needForPriceValue = filter_var($needForPriceValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        // Преобразуем not_for_printing.value в boolean
        $notForPrintingValue = $this->input('not_for_printing.value');
        if (is_string($notForPrintingValue)) {
            $notForPrintingValue = filter_var($notForPrintingValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        // Обновляем данные запроса с преобразованными значениями
        $this->merge([
            'additional_description' => $this->input('additional_description') ?? "",
            'description' => $this->input('description') ?? "",
            'ean_more' => $this->input('ean_more') ?? "",
            'sub_title' => $this->input('sub_title') ?? "",
            'text_italic' => $this->input('text_italic') ?? "",
            'title' => $this->input('title') ?? "",
            'need_for_price' => [
                'title' => $this->input('need_for_price.title'), // Сохраняем title
                'value' => $needForPriceValue,
            ],
            'not_for_printing' => [
                'title' => $this->input('not_for_printing.title'), // Сохраняем title
                'value' => $notForPrintingValue,
            ],
        ]);
    }

}
