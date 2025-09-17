<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionSurfacesDesign\AddDesignToSurfaceRequest;
use App\Http\Requests\PromotionSurfacesDesign\AddProductDesignRequest;
use App\Http\Requests\PromotionSurfacesDesign\BindDesignToYourselfRequest;
use App\Http\Requests\PromotionSurfacesDesign\DeleteFileBriefDesignRequest;
use App\Http\Requests\PromotionSurfacesDesign\GetAllPromotionSurfaces;
use App\Http\Requests\PromotionSurfacesDesign\GetPromotionSurfaceDesign;
use App\Http\Requests\PromotionSurfacesDesign\SendingNotificationPrintersRequest;
use App\Http\Requests\PromotionSurfacesDesign\SetFilesBriefDesignRequest;
use App\Http\Requests\PromotionSurfacesDesign\UpdateBriefSurfaceDesignRequest;
use App\Models\PromotionSurfaceDesign;
use App\Models\Test;
use App\Repositories\PromotionSurfacesDesignRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionSurfaceDesignController extends BaseController {

    protected PromotionSurfacesDesignRepository $promotionSurfacesDesignRepository;

    public function __construct(PromotionSurfacesDesignRepository $promotionSurfacesDesignRepository) {
        $this->promotionSurfacesDesignRepository = $promotionSurfacesDesignRepository;
    }

    /**
     * Все PromotionSurfaces с данными поверхности и всех дизайнов поверхности
     * @param GetAllPromotionSurfaces $request
     * @return JsonResponse
     */
    public function index(GetAllPromotionSurfaces $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->promotionSurfacesDesignRepository->getAllPromotionSurfaces($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Добавить дизайн к поверхности акции
     * @param AddDesignToSurfaceRequest $request
     * @return JsonResponse
     */
    public function store(AddDesignToSurfaceRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->promotionSurfacesDesignRepository->addDesignToSurfacePromotion($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Обновление данных brief дизайна
     * @param UpdateBriefSurfaceDesignRequest $request
     * @param int $promotion_surface_designs_id
     * @return JsonResponse
     */
    public function update(UpdateBriefSurfaceDesignRequest $request, int $promotion_surface_designs_id): JsonResponse {
        $validated = $request->validated();
        $promotion_surface_design = PromotionSurfaceDesign::find($promotion_surface_designs_id);
        if (!$promotion_surface_design) {
            return $this->getErrorResponse("Promotion surface designs not found", [], 404);
        }

        // Передаем данные в репозиторий
        $result = $this->promotionSurfacesDesignRepository->updateBriefSurfaceDesign($validated, $promotion_surface_design);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    // Удалить дизайн из поверхности акции
    public function destroy(Request $request, int $promotion_surface_designs_id): JsonResponse {
        // Текущий авторизованный пользователь
        $currentUser = $request->user();
        $design = PromotionSurfaceDesign::find($promotion_surface_designs_id);
        if (!$design) {
            return $this->getErrorResponse('Design not found', [], 404);
        }

        $result = $this->promotionSurfacesDesignRepository->deleteDesignFromSurfacePromotion(
            $currentUser,
            $design
        );

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Загрузить Promotion, Surface, Design по id
     *
     * @param GetPromotionSurfaceDesign $request
     * @return JsonResponse
     */
    public function getPromotionSurfaceDesign(GetPromotionSurfaceDesign $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->promotionSurfacesDesignRepository->getPromotionSurfaceDesign($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Добавление продукта в дизайн
     * @param AddProductDesignRequest $request
     * @return JsonResponse
     */
    public function addProductBriefDesign(AddProductDesignRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->promotionSurfacesDesignRepository->addProductBriefDesign($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Удаление продукта из дизайна
     * @param AddProductDesignRequest $request
     * @return JsonResponse
     */
    public function deleteProductBriefDesign(AddProductDesignRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->promotionSurfacesDesignRepository->deleteProductBriefDesign($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Сохранить файлы для Design Brief
     * @param SetFilesBriefDesignRequest $request
     * @return JsonResponse
     */
    public function setFilesBriefDesign(SetFilesBriefDesignRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->promotionSurfacesDesignRepository->setFilesBriefDesign($validated);

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Удалить файл из brief дизайна
     * @param AddProductDesignRequest $request
     * @return JsonResponse
     */
    public function deleteFileBriefDesign(DeleteFileBriefDesignRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->promotionSurfacesDesignRepository->deleteFileBriefDesign($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Оповестить юзеров (Printer) участвующих в этом Promotion о статусе Completed дизайнов их Surfaces
     *
     * @param SendingNotificationPrintersRequest $request
     * @return JsonResponse
     */
    public function sendingNotificationPrinters(SendingNotificationPrintersRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->promotionSurfacesDesignRepository->sendingNotificationPrinters($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Привязать Дизайн Поверхности в Promotion к себе
     *
     * @param BindDesignToYourselfRequest $request
     * @return JsonResponse
     */
    public function bindDesignToYourself(BindDesignToYourselfRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = PromotionSurfaceDesign::where('id', $validated['design_id'])
                ->update([
                    'designer_id' => $validated['bool_action'] ? $request->user()->id : null
                ]);

        if ($result > 0) {
            $message = $validated['bool_action'] ? 'Design successfully bound to you' : 'Design successfully detached';
            return $this->getSuccessResponse($message, [], 200);
        }
        else {
            return $this->getErrorResponse('Failed to update design', [], 400);
        }
    }

}
