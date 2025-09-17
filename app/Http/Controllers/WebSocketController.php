<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class WebSocketController extends BaseController {

    /**
     * Отправить клиенту ключ reverb
     * @return JsonResponse
     */
    public function getReverbData(): JsonResponse {
        // Получаем ключ из .env
        $key = env('REVERB_APP_KEY');
        $host = env('REVERB_HOST');

        if ($key && $host) {
            return $this->getSuccessResponse('', compact("key", "host"));
        }
        else {
            return $this->getErrorResponse("There is no reverb key", [], 401);
        }
    }

}

