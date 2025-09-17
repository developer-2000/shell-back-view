<?php

namespace App\Http\Requests\FeedbackMessage;

use Illuminate\Foundation\Http\FormRequest;

class CreateFeedbackRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'title' => 'required|string|max:255',
            'to_user_id' => 'required|integer',
            'message' => 'required|string',
        ];
    }

}
