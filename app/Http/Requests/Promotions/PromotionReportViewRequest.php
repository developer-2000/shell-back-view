<?php

namespace App\Http\Requests\Promotions;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PromotionReportViewRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'promotion_id' => 'required|integer|exists:promotions,id',
        ];
    }

}
