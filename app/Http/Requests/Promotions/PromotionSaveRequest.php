<?php

namespace App\Http\Requests\Promotions;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PromotionSaveRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    /**
     * Validation используется как при создании так и при обновлении user
     * @return string[]
     */
    public function rules() {
        $rules = [
            'name' => 'required|string|max:255',
            'period' => 'required|array',
            'show_in_user_promotions' => 'required|boolean',
            'description' => 'nullable|string',
            'surfaces' => 'array',
            'who_created_id' => 'required|integer|exists:users,id',
        ];

        return $rules;
    }

    // обработка полей перед валидацией
    protected function prepareForValidation() {
        $show_in_user_promotions = $this->input('show_in_user_promotions');
        if (is_string($show_in_user_promotions)) {
            $show_in_user_promotions = filter_var($show_in_user_promotions, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        // Проверка валидности поля date_picker
        $period = $this->input('period');

        if (
            is_array($period) &&
            isset($period['from']) &&
            isset($period['to']) &&
            !empty($period['from']) &&
            !empty($period['to'])
        ) {
            $fromDate = $period['from'];
            $toDate = $period['to'];

            try {
                $from = Carbon::parse($fromDate);
                $to = Carbon::parse($toDate);

                // Проверяем, что даты валидны и start <= end
                if (!$from || !$to || !$from->lte($to)) {
                    $period = null;
                }
            }
            catch (\Exception $e) {
                $period = null;
            }
        }
        else {
            $period = null;
        }

        $this->merge([
            'show_in_user_promotions' => $show_in_user_promotions,
            'period' => $period,
            'surfaces' => $this->input('surfaces', []),
        ]);
    }

}
