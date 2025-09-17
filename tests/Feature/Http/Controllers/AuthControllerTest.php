<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase; // Используем трейт для автоматического отката изменений в базе данных после каждого теста

    // Тест на успешный вход с валидными учетными данными
    public function test_can_login_with_valid_credentials()
    {
        // Запускаем сидеры для создания необходимых данных (например, пользователя с заданным email и паролем)
        $this->seed(); // Это вызовет DatabaseSeeder, который запускает все сидеры

        // Выполняем запрос на вход с валидными учетными данными
        $response = $this->postJson('/api/login', [
            'email' => 'admin@admin.com', // Валидный email
            'password' => 'password1', // Валидный пароль
        ]);

        // Проверяем, что статус ответа равен 200 (HTTP_OK)
        $response->assertStatus(JsonResponse::HTTP_OK)
            // Проверяем структуру JSON-ответа
            ->assertJsonStructure([
                'status', // Проверка наличия поля 'status'
                'message', // Проверка наличия поля 'message'
                'data' => [ // Проверка наличия поля 'data' с вложенной структурой
                    'mergedUserData' => [
                        'id', // Проверка наличия поля 'id' в 'mergedUserData'
                        'name', // Проверка наличия поля 'name'
                        'email', // Проверка наличия поля 'email'
                        'company_name', // Проверка наличия поля 'company_name'
                        'surname', // Проверка наличия поля 'surname'
                        'phone', // Проверка наличия поля 'phone'
                    ],
                    'bearerToken', // Проверка наличия поля 'bearerToken'
                ],
            ]);
    }

    // Тест на неуспешный вход с невалидными учетными данными
    public function test_cannot_login_with_invalid_credentials()
    {
        // Выполняем запрос на вход с невалидными учетными данными
        $response = $this->postJson('/api/login', [
            'email' => 'wrong@admin.com', // Неверный email
            'password' => 'wrongpassword', // Неверный пароль
        ]);

        // Проверяем, что статус ответа равен 401 (HTTP_UNAUTHORIZED)
        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            // Проверяем содержание JSON-ответа
            ->assertJson([
                'status' => 'error', // Ожидаем статус 'error'
                'message' => '', // Ожидаем пустое сообщение
                'errors' => [
                    'Error in entered data', // Ожидаем наличие ошибки
                ],
            ]);
    }

    // Тест на выход из системы для аутентифицированного пользователя
    public function test_can_logout_authenticated_user()
    {
        // Создаем аутентифицированного пользователя с помощью Sanctum
        Sanctum::actingAs(
            User::factory()->create() // Используем фабрику для создания пользователя
        );

        // Выполняем запрос на выход из системы
        $response = $this->postJson('/api/logout');

        // Проверяем, что статус ответа равен 200 (HTTP_OK)
        $response->assertStatus(JsonResponse::HTTP_OK)
            // Проверяем содержание JSON-ответа
            ->assertJson([
                'status' => 'success', // Ожидаем статус 'success'
                'message' => 'Logged out successfully', // Ожидаем сообщение о успешном выходе
                'data' => [], // Проверка на пустой массив данных
            ]);
    }

}
