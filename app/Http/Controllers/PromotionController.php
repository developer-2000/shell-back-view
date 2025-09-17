<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\Distributor\PromotionViewRequest;
use App\Http\Requests\Promotions\DeletePromotionRequest;
use App\Http\Requests\Promotions\GetStatusParcelsRequest;
use App\Http\Requests\Promotions\PromotionPaginationRequest;
use App\Http\Requests\Promotions\PromotionReportViewRequest;
use App\Http\Requests\Promotions\PromotionSaveRequest;
use App\Models\Promotion;
use App\Models\Test;
use App\Models\User;
use App\Repositories\PromotionRepository;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PromotionController extends BaseController {

    protected PromotionRepository $promotionRepository;

    public function __construct(PromotionRepository $designRepository) {
        $this->promotionRepository = $designRepository;
    }

    /**
     * Выбирает promotion в пагинации, с фильтрами и сортировкой для таблицы на странице Promotions
     * @param PromotionPaginationRequest $request
     * @return JsonResponse
     */
    public function index(PromotionPaginationRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

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

        $result = $this->promotionRepository->getPromotions($params, $currentUser);

        return $this->getSuccessResponse('', $result);
    }

    /**
     * Выборка полного обьекта promotion
     * @param Promotion $promotion
     * @return JsonResponse
     */
    public function show(Promotion $promotion): JsonResponse {
        if (!$promotion->exists) {
            return $this->getErrorResponse('Promotion not found', [], 404);
        }

        // 1 Загружаем связанную запись PrintPromotionReport
        $promotion->load('printPromotionReport');
        // 2 Загружаем пользователя, который создал Promotion
        $promotion->load('whoCreated');

        $history = [
            "creat_promotion"=>$promotion->created_at,
            "designer_completion_date"=>$promotion->send_to_printer,
            "printer_completion_date"=>$promotion->send_to_distributor,
            "distributor_complete_date"=>$promotion->complete_distributor_work,
        ];

        return $this->getSuccessResponse('', compact("promotion", "history"));
    }

    /**
     * Создание promotion
     * @param PromotionSaveRequest $request
     * @return JsonResponse
     */
    public function store(PromotionSaveRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        // Передаем данные в репозиторий для создания
        $result = $this->promotionRepository->createPromotion($validatedData, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Обновить данные promotion.
     * @param PromotionSaveRequest $request
     * @return JsonResponse
     */
    public function update(PromotionSaveRequest $request, Promotion $promotion): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий
        $result = $this->promotionRepository->updatePromotion($validatedData, $promotion);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Удаление promotion
     * @param DeletePromotionRequest $request
     * @return JsonResponse
     */
    public function destroy(Request $request, Promotion $promotion): JsonResponse {
        // Текущий авторизованный пользователь
        $currentUser = $request->user();
        if (!$promotion) {
            return $this->getErrorResponse('Promotion not found', [], 404);
        }

        // Передаем данные в репозиторий
        $result = $this->promotionRepository->deletePromotion($currentUser, $promotion);

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Загрузка данных promotion-report-view страницы
     * @param PromotionReportViewRequest $request
     * @return JsonResponse
     */
    public function promotionReportView(PromotionReportViewRequest $request): JsonResponse {
        $validated = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->promotionRepository->promotionReportView($validated, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Сформировать данные статусов посылок Printers
     *
     * @param GetStatusParcelsRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getStatusPrinterParcels(GetStatusParcelsRequest $request): JsonResponse {
        $validated = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->promotionRepository->getStatusPrinterParcels($validated, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Сформировать данные статусов посылок Distributors
     *
     * @param GetStatusParcelsRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getStatusDistributorParcels(GetStatusParcelsRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->promotionRepository->getStatusDistributorParcels($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Оповестить Admin и Активация этой Promotion
     *
     * @param GetStatusParcelsRequest $request
     * @return JsonResponse
     */
    public function notifyAdminAboutPromotion(GetStatusParcelsRequest $request): JsonResponse {
        $validated = $request->validated();

        $result = $this->promotionRepository->notifyAdminAboutPromotion($validated);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Выбрать всех CM и текущего юзера
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllCmAndI(Request $request): JsonResponse {
        // Выбрать всех CM юзеров
        $cm_users = User::whereHas('roles', function ($query) {
            $query->where('name', 'cm');
        })->select('id', 'name')->get();

        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        // добавить моего user в начало
        $cm_users = $cm_users->prepend([
            'id' => $currentUser->id,
            'name' => $currentUser->name,
        ]);

        return $this->getSuccessResponse('', compact( "cm_users"));
    }

}
