<?php

namespace App\Http\Requests\PromotionSurfacesDesign;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class BindDesignToYourselfRequest extends FormRequest {

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
            'design_id' => 'required|integer|exists:promotion_surface_designs,id',
            'bool_action' => 'required|boolean',
        ];
    }

    protected function prepareForValidation() {
        $bool_action = $this->input('bool_action');
        if (is_string($bool_action)) {
            $bool_action = filter_var($bool_action, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $this->merge([
            'bool_action' => $bool_action,
        ]);
    }

}
