<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\PrintPromotionReport\GetReport;
use App\Http\Requests\PrintPromotionReport\SetPrintedRequest;
use App\Http\Requests\PrintPromotionReport\GetPromotionsRequest;
use App\Repositories\PrintPromotionReportRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PrintPromotionReportController extends BaseController {

    protected PrintPromotionReportRepository $printPromotionReportRepository;

    public function __construct(PrintPromotionReportRepository $printPromotionReportRepository) {
        $this->printPromotionReportRepository = $printPromotionReportRepository;
    }

    /**
     * Выборка данных Report этого Printer для определенного Promotion
     * @param GetReport $request
     * @return JsonResponse
     */
    public function getReport(GetReport $request): JsonResponse {
        $validated = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->printPromotionReportRepository->getReport($validated, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Отправить Tracker посылки Принтера с Дизайнами в ней
     * @param SetPrintedRequest $request
     * @return JsonResponse
     */
    public function setPrinted(SetPrintedRequest $request): JsonResponse {
        $validated = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->printPromotionReportRepository->setPrinted($validated, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

}
