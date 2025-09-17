<?php

namespace App\Http\Requests\CompanyPlanner;

use App\Enums\CategoryGroup;
use App\Models\Test;
use Illuminate\Foundation\Http\FormRequest;
use \App\Models\User;
use Illuminate\Validation\Rule;

class SaveAmountCompanyRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules() {
        return [
            'surface_id' => 'required|integer|exists:surfaces,id',
            'amount' => 'required|integer|min:0',
        ];
    }

}
