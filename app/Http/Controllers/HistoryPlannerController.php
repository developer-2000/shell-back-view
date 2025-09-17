<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\HistoryPlanner\HistoryPaginationRequest;
use App\Repositories\HistoryPlannerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistoryPlannerController extends BaseController {

    protected HistoryPlannerRepository $historyPlannerRepository;

    public function __construct(HistoryPlannerRepository $historyPlannerRepository) {
        $this->historyPlannerRepository = $historyPlannerRepository;
    }


    public function index(HistoryPaginationRequest $request): JsonResponse {
        $validatedData = $request->validated();

        $params = [
            'count_show' => $validatedData['count_show'],
            'field' => $validatedData['obj_search']['field'] ?? '',
            'input_value' => $validatedData['obj_search']['input_value'] ?? '',
            'start_index' => $validatedData['start_index'],
            'sort_by' => $validatedData['sort_by'] ?? '',
            'sort_count' => $validatedData['sort_count'],
        ];

        $result = $this->historyPlannerRepository->getHistory($params);

        return $this->getSuccessResponse('', $result);
    }

}
