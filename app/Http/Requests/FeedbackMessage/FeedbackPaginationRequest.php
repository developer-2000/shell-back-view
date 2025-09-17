<?php

namespace App\Http\Requests\FeedbackMessage;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class FeedbackPaginationRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'count_show' => 'required|integer|min:10',
            'start_index' => 'required|integer|min:0'
        ];
    }

}
