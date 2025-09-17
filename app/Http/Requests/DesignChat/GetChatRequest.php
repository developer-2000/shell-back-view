<?php

namespace App\Http\Requests\DesignChat;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class GetChatRequest extends FormRequest {

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
            'chat_id' => 'required|integer',
        ];
    }

}
