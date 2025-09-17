<?php

namespace App\Http\Controllers;

use App\Http\Requests\DesignChat\CreateMessageRequest;
use App\Http\Requests\DesignChat\DeleteMessageRequest;
use App\Http\Requests\DesignChat\UpdateSwitchRatingImageRequest;
use App\Http\Requests\PromotionSurfacesDesign\GetChatRequest;
use App\Models\DesignChat;
use App\Repositories\DesignChatRepository;
use Illuminate\Http\JsonResponse;

class DesignChatController extends BaseController {

    protected DesignChatRepository $designChatRepository;

    public function __construct(DesignChatRepository $designChatRepository) {
        $this->designChatRepository = $designChatRepository;
    }

    /**
     * Выборка Дизайн чата по id
     * @param GetChatRequest $request
     * @return JsonResponse
     */
    public function index(GetChatRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->designChatRepository->getChat($validatedData, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Создать новое сообщение в чате
     * @param CreateMessageRequest $request
     * @return JsonResponse
     */
    public function store(CreateMessageRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->designChatRepository->createMessage($validatedData, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Обновить свое сообщение в чате
     * @param CreateMessageRequest $request
     * @return JsonResponse
     */
    public function updateMessage(CreateMessageRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->designChatRepository->updateMessage($validatedData, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Удалить свое сообщение в чате
     * @param DeleteMessageRequest $request
     * @return JsonResponse
     */
    public function deleteMessage(DeleteMessageRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->designChatRepository->deleteMessage($validatedData, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Установить во всем чате статус - мной прочитано
     * @param GetChatRequest $request
     * @return JsonResponse
     */
    public function setReadStatusMessages(GetChatRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->designChatRepository->setReadStatusMessages($validatedData, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Обновить надпись в переключателе rating image для менеджера
     * И статус дизайна этого чата
     * @param \App\Http\Requests\DesignChat\UpdateSwitchRatingImageRequest $request
     * @return JsonResponse
     */
    public function updateSwitchRatingImage(UpdateSwitchRatingImageRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->designChatRepository->updateSwitchRatingImage($validatedData, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

}
