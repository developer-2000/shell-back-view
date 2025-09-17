<?php

namespace App\Http\Requests\PromotionSurfaces;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ChangeSurfacesAtPromotionRequest extends FormRequest {

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
            'from_promotion_id' => 'required|exists:promotions,id',
            'whom_promotion_id' => 'required|exists:promotions,id',
        ];
    }

}
