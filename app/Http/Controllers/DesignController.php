<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\Designs\DeleteDesignRequest;
use App\Http\Requests\Designs\DesignPaginationRequest;
use App\Http\Requests\Designs\DesignSaveRequest;
use App\Models\Design;
use App\Repositories\DesignRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesignController extends BaseController {

    protected DesignRepository $designRepository;

    public function __construct(DesignRepository $designRepository) {
        $this->designRepository = $designRepository;
    }

    /**
     * Выбирает дизайны в пагинации, с фильтрами и сортировкой для таблицы на странице Designs
     * @param DesignPaginationRequest $request
     * @return JsonResponse
     */
    public function index(DesignPaginationRequest $request): JsonResponse {
        $validatedData = $request->validated();

        $params = [
            'count_show' => $validatedData['count_show'],
            'field' => $validatedData['obj_search']['field'] ?? '',
            'input_value' => $validatedData['obj_search']['input_value'] ?? '',
            'only_category' => $validatedData['obj_search']['only_category'] ?? '',
            'start_index' => $validatedData['start_index'],
            'sort_by' => $validatedData['sort_by'] ?? '',
            'sort_count' => $validatedData['sort_count'],
        ];

        $result = $this->designRepository->getDesigns($params);

        return $this->getSuccessResponse('', $result);
    }

    /**
     * Создание дизайна
     * @param DesignSaveRequest $request
     * @return JsonResponse
     */
    public function store(DesignSaveRequest $request): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий для создания
        $result = $this->designRepository->createDesign($validatedData);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Обновить данные дизайна.
     * @param DesignSaveRequest $request
     * @return JsonResponse
     */
    public function update(DesignSaveRequest $request, Design $design): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий для создания
        $result = $this->designRepository->updateDesign($validatedData, $design);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Мягкое удаление design
     */
    public function destroy(Request $request, Design $design): JsonResponse {
        // Текущий авторизованный пользователь
        $currentUser = $request->user();
        if (!$design) {
            return $this->getErrorResponse('Design not found', [], 404);
        }

        $result = $this->designRepository->deleteDesign($currentUser, $design);

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Все дизайны проекта
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllDesigns(): JsonResponse {
        $designs = Design::all();

        return $this->getSuccessResponse('', compact("designs"));
    }

}
