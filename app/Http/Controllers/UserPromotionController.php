<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\Promotions\DeletePromotionRequest;
use App\Http\Requests\Promotions\PromotionPaginationRequest;
use App\Http\Requests\Promotions\PromotionSaveRequest;
use App\Models\Promotion;
use App\Models\Test;
use App\Repositories\PromotionRepository;
use App\Repositories\UserPromotionRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserPromotionController extends BaseController {

    protected UserPromotionRepository $userPromotionRepository;

    public function __construct(UserPromotionRepository $userPromotionRepository) {
        $this->userPromotionRepository = $userPromotionRepository;
    }

    /**
     * Выбирает promotion в пагинации, с фильтрами и сортировкой для таблицы на странице Promotions
     * @param PromotionPaginationRequest $request
     * @return JsonResponse
     */
    public function index(PromotionPaginationRequest $request): JsonResponse {
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

        $result = $this->userPromotionRepository->getPromotions($params);

        return $this->getSuccessResponse('', $result);
    }

}
