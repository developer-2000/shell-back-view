<?php

namespace App\Http\Requests\PromotionSurfacesDesign;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class GetPromotionSurfaceDesign extends FormRequest {

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
            'surface_design_id' => 'required|integer',
        ];
    }

}
