<?php

namespace App\Http\Requests\PromotionSurfacesDesign;

use Illuminate\Foundation\Http\FormRequest;

class DeleteFileBriefDesignRequest extends FormRequest {

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
            'promotion_surface_design_id' => ['required', 'integer', 'exists:promotion_surface_designs,id'],
            'file_id' => ['required', 'integer'],
        ];
    }

}
