<?php

namespace App\Http\Requests\PrintPromotionReport;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class GetReport extends FormRequest {

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
            'promotion_id' => 'required|integer|exists:print_promotion_reports,promotion_id',
        ];
    }

}
