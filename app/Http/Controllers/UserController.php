<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Events\AddNewMessageEvent;
use App\Http\Requests\Users\DeleteUserRequest;
use App\Http\Requests\Users\GetManagerCategoryRequest;
use App\Http\Requests\Users\GetUsersByFieldRequest;
use App\Http\Requests\Users\GetUsersIdsRequest;
use App\Http\Requests\Users\UpdatePasswordRequest;
use App\Http\Requests\Users\UserSaveRequest;
use App\Http\Requests\Users\UserPaginationRequest;
use App\Models\Category;
use App\Models\CompanyPlanner;
use App\Models\DistributorTracker;
use App\Models\Surface;
use App\Models\Test;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use App\Models\Role;
use Illuminate\Http\Request;


class UserController extends BaseController {

    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * Выбирает юзеров в пагинации, с фильтрами и сортировкой для таблицы на странице Users
     * @param \App\Http\Requests\Users\UserPaginationRequest $request
     * @return JsonResponse
     */
    public function index(UserPaginationRequest $request): JsonResponse {
        $validatedData = $request->validated();

        $params = [
            'count_show' => $validatedData['count_show'],
            'admin_only' => $validatedData['obj_search']['admin_only'],
            'field' => $validatedData['obj_search']['field'] ?? '',
            'input_value' => $validatedData['obj_search']['input_value'] ?? '',
            'start_index' => $validatedData['start_index'],
            'sort_by' => $validatedData['sort_by'] ?? '',
            'sort_count' => $validatedData['sort_count'],
        ];

        $result = $this->userRepository->getUsers($params);

        return $this->getSuccessResponse('', $result);
    }

    /**
     * Создать нового пользователя.
     * @return JsonResponse
     */
    public function store(UserSaveRequest $request): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий для создания пользователя
        $result = $this->userRepository->createUser($validatedData);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], 201);
        }

        return $this->getErrorResponse($result['message'], [], 500);
    }

    /**
     * Обновить данные пользователя.
     * @return JsonResponse
     */
    public function update(UserSaveRequest $request, User $user): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий для создания пользователя
        $result = $this->userRepository->updateUser($validatedData, $user);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Мягкое удаление user
     * @param DeleteUserRequest $request
     * @return JsonResponse
     */
    public function destroy(Request $request, User $user): JsonResponse {
        // Текущий авторизованный пользователь
        $currentUser = $request->user();
        if (!$user) {
            return $this->getErrorResponse('User not found', [], 404);
        }

        $result = $this->userRepository->deleteUser($currentUser, $user);

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Обновление пароля юзера админом
     * @param UpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse {
        // Текущий авторизованный пользователь
        $currentUser = $request->user();
        $result = $this->userRepository->updatePassword($currentUser, $request->validated());

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Получает пользователей по указанному полю и значению.
     *
     * @param GetUsersByFieldRequest $request
     * @return JsonResponse
     */
    public function getUsersByField(GetUsersByFieldRequest $request): JsonResponse {
        $field = $request->input('field');
        $value = $request->input('value');

        // Используем метод репозитория для получения данных
        $users = $this->userRepository->getUsersByField($field, $value);

        return $this->getSuccessResponse('', compact("users"));
    }

    /**
     * Выбрать users по id в массиве
     * @param GetUsersIdsRequest $request
     * @return JsonResponse
     */
    public function getUsersIds(GetUsersIdsRequest $request): JsonResponse {
        $userIds = $request->users_ids;

        // Если массив пустой, возвращаем пустой массив пользователей
        if (empty($userIds)) {
            return $this->getSuccessResponse('No user IDs provided.', ['users' => []]);
        }

        // Получаем пользователей, чьи id находятся в массиве $userIds
        $users = User::whereIn('id', $userIds)
            ->with('userData', 'roles')
            ->get();

        // Преобразуем данные, используя accessor
        $mergedUsers = $users->map(function ($user) {
            return $user->merged_user_data;
        });

        return $this->getSuccessResponse('', ['users' => $mergedUsers]);
    }

    /**
     * Выбрать первую категорию менеджера
     * @param GetManagerCategoryRequest $request
     * @return JsonResponse
     */
    public function getManagerCategory(GetManagerCategoryRequest $request) {
        $validatedData = $request->validated();

        $category = Category::where('manager_id', $validatedData["user_id"])->first();

        if ($category) {
            return $this->getSuccessResponse('', compact("category"));
        } else {
            return $this->getSuccessResponse('', []);
        }
    }

    /**
     * Получить все роли.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRoles(Request $request): JsonResponse {
        try {
            // Получаем все роли, выбирая только поле 'name'
            $roles = Role::pluck('name')->toArray();

            return $this->getSuccessResponse('', compact("roles"));
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserCompanyPlanner(Request $request): JsonResponse {
        // Текущий авторизованный пользователь
        $currentUser = $request->user();
        $surfacesArr = [];

        $surfacesPlaner = CompanyPlanner::where("user_id", $currentUser->id)->first();

        // Проверяем, существует ли запись CompanyPlanner для пользователя
        if ($surfacesPlaner && $surfacesPlaner->surfaces) {
            foreach ($surfacesPlaner->surfaces as $surface) {
                list($surfaceId, $amount) = explode("_", $surface);
                $surfacesArr[] = [
                    "surface_id" => (int) $surfaceId,
                    "amount" => (int) $amount
                ];
            }
        }

        $distributor = DistributorTracker::where("company_id", $currentUser->id)->get();

        return $this->getSuccessResponse('', compact("surfacesArr", "distributor"));
    }
}
