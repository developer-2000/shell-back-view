<?php

namespace App\Http\Requests\PrintPromotionReport;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class SetPrintedRequest extends FormRequest {

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
            'printer_tracker_number' => 'nullable|string',
            'sent_surfaces' => 'required|array',
            'description' => 'nullable|string',
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'printer_tracker_number' => $this->input('printer_tracker_number', null),
            'description' => $this->input('description', ""),
        ]);
    }

}
