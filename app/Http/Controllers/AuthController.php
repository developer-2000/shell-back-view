<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\ToReLoginRequest;
use App\Models\ReLogin;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController {

    /**
     * User authorization
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user()->load('userData', 'roles');
            // Удаление всех токенов текущего пользователя перед созданием нового
//            $user->tokens()->delete();
            // Создать новый токен
            $bearerToken = $user->createToken('Personal Access Token')->plainTextToken;

            // Проверка если входит user - убрать переход на admin
            $reLoginRecord = ReLogin::where("to_user_id", $user->id)->first();
            if ($reLoginRecord) {
                // Удалить запись ReLogin из базы данных
                $reLoginRecord->delete();
            }

            // Используем accessor для получения объединённых данных
            $mergedUserData = $user->merged_user_data;

            return $this->getSuccessResponse('Login successful', compact("mergedUserData", "bearerToken"));
        }

        return $this->getErrorResponse("The data is incorrect", [], 401);
    }

    /**
     * User Logout
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();

        return $this->getSuccessResponse('Logged out successfully', []);
    }

    /**
     * Ре-логин под id юзера
     * @param ToReLoginRequest $request
     * @return JsonResponse
     */
    public function toReLogin(ToReLoginRequest $request): JsonResponse {
        $validatedData = $request->validated();
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        // Проверка на авторизацию
        $this->checkAuthorization($currentUser);

        if (!$this->hasAdminRole($currentUser, ['admin', 'cm-admin'])) {
            return $this->getErrorResponse("Unauthorized access", [], 403);
        }

        // Получаем пользователя по user_id
        $user = User::with('userData', 'roles')->find($validatedData["user_id"]);

        // Зафиксировать Re-login
        ReLogin::updateOrCreate(
            ["from_user_id" => $currentUser->id],
            ["to_user_id" => $user->id]
        );

        // Создать для пользователя токен и удалить текущий токен
        return $this->createTokenAndDeleteCurrent($currentUser, $user);
    }

    /**
     * Обратный Ре-логин под id юзера в admin
     * @param ToReLoginRequest $request
     * @return JsonResponse
     */
    public function backReLogin(Request $request): JsonResponse {
        // Текущий авторизованный пользователь
        $currentUser = $request->user();

        // Проверка на авторизацию
        $this->checkAuthorization($currentUser);

        // Запись ReLogin
        $reLoginRecord = ReLogin::where("to_user_id", $currentUser->id)->first();

        if (!$reLoginRecord) {
            return $this->getErrorResponse("No ReLogin record found", [], 404);
        }

        // Получаем admin
        $user = User::with('userData', 'roles')->find($reLoginRecord->from_user_id);

        if (!$user) {
            return $this->getErrorResponse("Admin user not found", [], 404);
        }

        // Удалить запись ReLogin из базы данных
        $reLoginRecord->delete();

        // Создать для admin токен и удалить текущий токен
        return $this->createTokenAndDeleteCurrent($currentUser, $user);
    }

    /**
     * Проверка авторизации и наличия ролей
     * @param $currentUser
     * @return JsonResponse|null
     */
    private function checkAuthorization($currentUser): ?JsonResponse {
        if (!$currentUser) {
            return $this->getErrorResponse("The requested user is unknown", [], 401);
        }

        if ($currentUser->roles->isEmpty()) {
            return $this->getErrorResponse("User has no assigned roles", [], 403);
        }

        return null;
    }

    /**
     * Создание токена для пользователя и удаление текущего токена
     * @param $currentUser
     * @param $user
     * @return JsonResponse
     */
    private function createTokenAndDeleteCurrent($currentUser, $user): JsonResponse {
        // Создать токен для пользователя
        $bearerToken = $user->createToken('Personal Access Token')->plainTextToken;

        // Удаляем текущий токен
        $currentToken = $currentUser->currentAccessToken();
        if ($currentToken) {
            $currentToken->delete();
        }

        // Полные данные user
        $mergedUserData = $user->merged_user_data;

        return $this->getSuccessResponse('Login successful', compact("mergedUserData", "bearerToken"));
    }

    /**
     * Проверка роли юзера
     * @param $user
     * @param array $roles
     * @return bool
     */
    private function hasAdminRole($user, array $roles): bool {
        return $user->roles->contains(function ($role) use ($roles) {
            return in_array($role->name, $roles);
        });
    }

}

