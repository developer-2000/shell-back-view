<?php

namespace App\Http\Requests\Promotions;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class GetStatusParcelsRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'promotion_id' => 'required|integer|exists:promotions,id',
        ];
    }

}
