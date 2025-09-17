<?php

namespace App\Http\Requests\PromotionSurfacesDesign;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AddProductDesignRequest extends FormRequest {

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
            'product_id' => 'required|integer|exists:products,id',
            'promotion_surface_design_id' => 'required|integer|exists:promotion_surface_designs,id',
        ];
    }

}
