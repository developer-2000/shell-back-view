<?php

namespace App\Http\Requests\PromotionSurfaces;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AddSurfaceInPromotionRequest extends FormRequest {

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
            'promotion_id' => 'required|exists:promotions,id',
            'surface_id' => 'required|exists:surfaces,id',
        ];
    }

}
