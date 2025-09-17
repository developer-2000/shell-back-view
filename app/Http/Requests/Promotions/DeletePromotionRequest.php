<?php

namespace App\Http\Requests\Promotions;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class DeletePromotionRequest extends FormRequest {

    /**
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array {
        return [
            'promotion_id' => 'required|integer|exists:promotions,id',
        ];
    }

}
