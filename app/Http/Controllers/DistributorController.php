<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\Distributor\GetPromotionsRequest;
use App\Http\Requests\Distributor\SetDistributorTrackerDataRequest;
use App\Repositories\DistributorRepository;
use Illuminate\Http\JsonResponse;

class DistributorController extends BaseController {

    protected DistributorRepository $distributorRepository;

    public function __construct(DistributorRepository $distributorRepository) {
        $this->distributorRepository = $distributorRepository;
    }

    /**
     * Выборка всех Promotions по таблице PrintedPromotions
     *
     * @param GetPromotionsRequest $request
     * @return JsonResponse
     */
    public function getPromotions(GetPromotionsRequest $request): JsonResponse {
        $validatedData = $request->validated();

        $params = [
            'count_show' => $validatedData['count_show'],
            'start_index' => $validatedData['start_index'],
            'field' => $validatedData['obj_search']['field'] ?? '',
            'input_value' => $validatedData['obj_search']['input_value'] ?? '',
            'only_active' => $validatedData['obj_search']['only_active'],
            'date_picker' => $validatedData['obj_search']['date_picker'] ?? '',
            'sort_by' => $validatedData['sort_by'] ?? '',
            'sort_count' => $validatedData['sort_count'],
        ];

        $result = $this->distributorRepository->getPromotions($params);

        return $this->getSuccessResponse('', $result);
    }

    /**
     * Установить Трекер номера Дистрибьютера
     *
     * @param SetDistributorTrackerDataRequest $request
     * @return JsonResponse
     */
    public function setDistributorTracker(SetDistributorTrackerDataRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->distributorRepository->setDistributorTracker($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

}
