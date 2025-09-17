<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\CompanyPlanner\CompanyPaginationRequest;
use App\Http\Requests\CompanyPlanner\SaveAmountCompanyRequest;
use App\Http\Requests\Surfaces\SurfacePaginationRequest;
use App\Repositories\CompanyPlannerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyPlannerController extends BaseController {

    protected CompanyPlannerRepository $companyPlannerRepository;

    public function __construct(CompanyPlannerRepository $companyPlannerRepository) {
        $this->companyPlannerRepository = $companyPlannerRepository;
    }

    /**
     * Выбрать все Surfaces с их категориями в пагинации и фильтрами
     * @param SurfacePaginationRequest $request
     * @return JsonResponse
     */
    public function index(CompanyPaginationRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $params = [
            'count_show' => $validatedData['count_show'],
            'field' => $validatedData['obj_search']['field'] ?? '',
            'input_value' => $validatedData['obj_search']['input_value'] ?? '',
            'only_quantity' => $validatedData['obj_search']['only_quantity'],
            'start_index' => $validatedData['start_index'],
            'sort_by' => $validatedData['sort_by'] ?? '',
            'sort_count' => $validatedData['sort_count'],
        ];

        $result = $this->companyPlannerRepository->getSurfaces($params, $currentUser);

        return $this->getSuccessResponse('', $result);
    }

    /**
     * Изменить Amount у категории поверхности
     * @param SaveAmountCompanyRequest $request
     * @return JsonResponse
     */
    public function saveAmountCompanyPlanner(SaveAmountCompanyRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->companyPlannerRepository->saveAmountCompanyPlanner($validatedData, $currentUser);

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

}
