<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionSurfaces\AddSurfaceInPromotionRequest;
use App\Http\Requests\PromotionSurfaces\ChangeSurfacesAtPromotionRequest;
use App\Models\PromotionSurface;
use App\Repositories\PromotionSurfacesRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionSurfaceController extends BaseController {

    protected PromotionSurfacesRepository $promotionSurfacesRepository;

    public function __construct(PromotionSurfacesRepository $promotionSurfacesRepository) {
        $this->promotionSurfacesRepository = $promotionSurfacesRepository;
    }

    /**
     * Выбрать все акции с определенными полями
     * @return JsonResponse
     */
    public function index(): JsonResponse {
        $result = $this->promotionSurfacesRepository->getAllPromotions();

        if ($result['success']) {
            return $this->getSuccessResponse('', $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse('Unknown error', [], $result['status_code']);
    }

    /**
     * Добавить surface в promotion
     * @param AddSurfaceInPromotionRequest $request
     * @return JsonResponse
     */
    public function store(AddSurfaceInPromotionRequest $request): JsonResponse {
        $validated = $request->validated();

        // Передаем данные в репозиторий для создания
        $result = $this->promotionSurfacesRepository->addSurfaceInPromotion($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Поменять все surfaces у promotion
     * @param int $from_promotion_id
     * @param int $whom_promotion_id
     * @return JsonResponse
     */
    public function update(int $from_promotion_id, int $whom_promotion_id): JsonResponse {
        $result = $this->promotionSurfacesRepository->changeSurfacesAtPromotion($from_promotion_id, $whom_promotion_id);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Удалить surface из promotion
     * @param int $promotion_id
     * @param int $surface_id
     * @return JsonResponse
     */
    public function destroy(int $promotion_id, int $surface_id): JsonResponse {
        $result = $this->promotionSurfacesRepository->deleteSurfaceInPromotion($promotion_id, $surface_id);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

}
