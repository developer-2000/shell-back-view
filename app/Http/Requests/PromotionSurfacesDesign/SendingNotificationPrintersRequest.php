<?php

namespace App\Http\Requests\PromotionSurfacesDesign;

use Illuminate\Foundation\Http\FormRequest;

class SendingNotificationPrintersRequest extends FormRequest {

    /**
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array {
        return [
            'promotion_id' => ['required', 'integer', 'exists:promotion_surface_designs,promotion_id'],
            'percent_report' => ['required', 'integer'],
            'description_cm' => 'nullable|string',
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'description_cm' => $this->input('description_cm') ?? "",
        ]);
    }

}
