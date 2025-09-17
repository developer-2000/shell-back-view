<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\FeedbackMessage\AddMessageRequest;
use App\Http\Requests\FeedbackMessage\CreateFeedbackRequest;
use App\Http\Requests\FeedbackMessage\FeedbackPaginationRequest;
use App\Http\Requests\FeedbackMessage\GetFeedbackRequest;
use App\Models\FeedbackMessage;
use App\Models\SystemSetting;
use App\Models\User;
use App\Repositories\FeedbackRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FeedbackMessagesController extends BaseController {

    protected FeedbackRepository $feedbackRepository;

    public function __construct(FeedbackRepository $feedbackRepository) {
        $this->feedbackRepository = $feedbackRepository;
    }

    /**
     * Выборка в пагинации всех моих Feedback
     *
     * @param FeedbackPaginationRequest $request
     * @return JsonResponse
     */
    public function index(FeedbackPaginationRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        // Получаем общее количество записей
        $query = FeedbackMessage::where("from_user_id", $currentUser->id)
            ->orWhere("to_user_id", $currentUser->id);
        $total = $query->count();

        $feedbacks = $query
            ->with('fromUser','fromUser.roles', 'toUser', 'toUser.roles')
            ->skip($validatedData['start_index'])
            ->take($validatedData['count_show'])
            ->get();

        if ($feedbacks) {
            return $this->getSuccessResponse('', compact('feedbacks', 'total'));
        }

        return $this->getErrorResponse('', []);
    }

    /**
     * Создает новый Feedback
     *
     * @param CreateFeedbackRequest $request
     * @return JsonResponse
     */
    public function store(CreateFeedbackRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->feedbackRepository->createFeedback($validatedData, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], []);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Добавить новый message в Feedback
     *
     * @param AddMessageRequest $request
     * @return JsonResponse
     */
    public function addMessage(AddMessageRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        $result = $this->feedbackRepository->addMessage($validatedData, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Выбрать данные для создания Message
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getInitializationData(Request $request): JsonResponse {
        // Текущий авторизованный пользователь
        $currentUser = $request->user();
        $users = [];
        $settings = SystemSetting::first();

        // user (Company), 'cm-admin', 'printer'
        if ($currentUser->hasRoles(['user', 'cm-admin', 'printer'])) {
            if ($settings) {
                $users = User::whereIn('id', [$settings->admin_id, $settings->distributor_id])
                    ->select("id", "name")
                    ->get();
            }
        }
        // designer
        else if ($currentUser->hasRole('designer')) {
            if ($settings) {
                $users = User::whereIn('id', [$settings->admin_id])
                    ->select("id", "name")
                    ->get();
            }
        }
        // admin
        else if ($currentUser->hasRole('admin')) {
            $users = User::select("id", "name")
                ->get();
        }

        // Исключаем текущего пользователя из выборки, если он есть
        $users = $users->filter(function($user) use ($currentUser) {
            return $user->id !== $currentUser->id;
        })->values();

        return $this->getSuccessResponse("", compact("users"));
    }

    /**
     * Выбрать указанный feedback
     *
     * @param GetFeedbackRequest $request
     * @return JsonResponse
     */
    public function getFeedbackData(GetFeedbackRequest $request): JsonResponse {
        $validatedData = $request->validated();
        $currentUser = $request->user();

        $result = $this->feedbackRepository->getFeedbackData($validatedData, $currentUser);

        if ($result['success']) {
            return $this->getSuccessResponse('', $result['data']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

}
