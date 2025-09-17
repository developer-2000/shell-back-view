<?php
namespace App\Http\Requests\DesignChat;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\RatingStatus;
use Illuminate\Validation\Rule;

class UpdateSwitchRatingImageRequest extends FormRequest {

    /**
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array {
        return [
            'rating' => [
                'required',
                'string',
                Rule::enum(RatingStatus::class),
            ],
            'chat_id' => 'required|exists:design_chats,id',
            'message_id' => 'required|integer',
        ];
    }

}
