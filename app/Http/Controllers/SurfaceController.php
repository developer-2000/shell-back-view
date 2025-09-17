<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\Surfaces\SizeSurfaceRequest;
use App\Http\Requests\Surfaces\SurfaceCloneRequest;
use App\Http\Requests\Surfaces\SurfacePaginationRequest;
use App\Http\Requests\Surfaces\SurfaceSaveRequest;
use App\Http\Requests\Surfaces\SurfaceUpdateRequest;
use App\Http\Requests\Surfaces\TypeSurfaceRequest;
use App\Models\Surface;
use App\Repositories\SurfaceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SurfaceController extends BaseController {

    protected SurfaceRepository $surfaceRepository;

    public function __construct(SurfaceRepository $surfaceRepository) {
        $this->surfaceRepository = $surfaceRepository;
    }

    /**
     * Выбирает поверхностей в пагинации, с фильтрами и сортировкой для таблицы на странице Surfaces
     * @param SurfacePaginationRequest $request
     * @return JsonResponse
     */
    public function index(SurfacePaginationRequest $request): JsonResponse {
        $validatedData = $request->validated();

        $params = [
            'count_show' => $validatedData['count_show'],
            'field' => $validatedData['obj_search']['field'] ?? '',
            'input_value' => $validatedData['obj_search']['input_value'] ?? '',
            'start_index' => $validatedData['start_index'],
            'sort_by' => $validatedData['sort_by'] ?? '',
            'sort_count' => $validatedData['sort_count'],
        ];

        $result = $this->surfaceRepository->getSurfaces($params);

        return $this->getSuccessResponse('', $result);
    }

    /**
     * Создание поверхности
     * @param SurfaceSaveRequest $request
     * @return JsonResponse
     */
    public function store(SurfaceSaveRequest $request): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий для создания
        $result = $this->surfaceRepository->createSurface($validatedData);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Обновить данные поверхности.
     * @param SurfaceSaveRequest $request
     * @return JsonResponse
     */
    public function update(SurfaceUpdateRequest $request, Surface $surface): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий
        $result = $this->surfaceRepository->updateSurface($validatedData, $surface);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Мягкое удаление surface
     */
    public function destroy(Request $request, Surface $surface): JsonResponse {
        // Текущий авторизованный пользователь
        $currentUser = $request->user();
        if (!$surface) {
            return $this->getErrorResponse('Surface not found', [], 404);
        }

        $result = $this->surfaceRepository->deleteSurface($currentUser, $surface);

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Создание нового типа поверхности.
     * @param TypeSurfaceRequest $request
     * @return JsonResponse
     */
    public function createTypeSurface(TypeSurfaceRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->surfaceRepository->createTypeSurface($validated);

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Создает новый размер поверхности
     * @param SizeSurfaceRequest $request
     * @return JsonResponse
     */
    public function createSizeSurface(SizeSurfaceRequest $request): JsonResponse {
        $validated = $request->validated();
        $result = $this->surfaceRepository->createSizeSurface($validated);

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Клонирует surface и создает дубликаты в таблицах Surface и CompanyPlanner
     *
     * @param SurfaceCloneRequest $request
     * @return JsonResponse
     */
    public function createCloneSurface(SurfaceCloneRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->surfaceRepository->createCloneSurface($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Выбрать все surfaces
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllSurfaces(): JsonResponse {
        $surfaces = Surface::all();

        return $this->getSuccessResponse('', compact("surfaces"));
    }

}
