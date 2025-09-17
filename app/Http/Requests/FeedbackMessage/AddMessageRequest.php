<?php

namespace App\Http\Requests\FeedbackMessage;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AddMessageRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'feedback_id' => 'required|integer|exists:feedback_messages,id',
            'message' => 'required|string',
        ];
    }

}
