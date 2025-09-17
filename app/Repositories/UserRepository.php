<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Test;
use App\Models\User;
use App\Models\UserData;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Exception;

class UserRepository extends BaseRepository {
    /**
     * Получает список пользователей с пагинацией, фильтрацией и сортировкой.
     *
     * @param array $params Параметры для фильтрации и сортировки.
     * @return array Массив с пользователями и общим числом записей.
     */
    public function getUsers(array $params): array {
        $query = $this->baseQuery();

        // Выбираем необходимые поля
        $this->selectFields($query);

        // 1 Фильтрация только для администраторов
        if ($params['admin_only']) {
            $query->where('roles.name', 'admin');
        }

        // 2 Применяем поиск-фильт по указанному полю
        $query = $this->applySearchFilter($query, $params['field'], $params['input_value']);

        // 3 Применяем сортировку по sort_by и sort_count
        $sortableFields = [
            'name' => 'users.name',
            'company_name' => 'user_data.company_name',
            'role' => 'roles.name',
            'email' => 'users.email',
            'phone' => 'user_data.phone',
            'post_address' => 'user_data.post_address',
            'status' => 'users.status',
        ];

        // Сортировка таблицы по полю
        $this->applySorting($query, $params, $sortableFields, 'users.name');

        // Получаем общее количество записей
        $total = $query->count();

        // Получаем отсортированные и постраничные данные
        $users = $query->skip($params['start_index'])->take($params['count_show'])->get();
        // Все категории из базы
        $categories = Category::select('id', 'name')->get()->toArray();

        return [
            'users' => $users,
            'total' => $total,
            'arr_categories' => $categories
        ];
    }

    /**
     * Создает нового пользователя
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом создания .
     */
    public function createUser(array $validated): array {
        try {
            // Начало транзакции
            DB::beginTransaction();

            $userData = $this->prepareUserData($validated);

            // Данные для таблицы users
            $userDataForUser = [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'status' => $userData['status'],
                'email_verified_at' => Carbon::now(),
            ];

            // Сохраняем пользователя
            $user = User::create($userDataForUser);
            if (!$user) {
                DB::rollBack();
                return ['success' => false, 'message' => 'Failed to create user in users table'];
            }

            // Обрабатываем роль
            if (!$this->handleUserRole($user, $userData['role'])) {
                DB::rollBack();
                return ['success' => false, 'message' => 'User role not found'];
            }

            // Сохраняем данные в user_data
            if (!$this->saveUserData($user, $userData)) {
                DB::rollBack();
                return ['success' => false, 'message' => 'Failed to create user data'];
            }

            // Фиксация транзакции
            DB::commit();
            return ['success' => true, 'message' => 'User created successfully!'];
        }
        catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * Обновляет данные пользователя
     *
     * @param array $validated
     * @return array
     */
    public function updateUser(array $validated, $user): array {
        try {
            // Начало транзакции
            DB::beginTransaction();

            $userData = $this->prepareUserData($validated, false);

            // Данные для таблицы users
            $userDataForUser = [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'status' => $userData['status'],
            ];

            $updateSuccessful = $user->update($userDataForUser);
            // Обновляем пользователя
            if (!$updateSuccessful) {
                DB::rollBack();
                return ['success' => false, 'message' => 'Failed to update user in users table', 'status_code' => 404];
            }

            // Обрабатываем роль
            if (!$this->handleUserRole($user, $userData['role'])) {
                DB::rollBack();
                return ['success' => false, 'message' => 'User role not found', 'status_code' => 404];
            }

            // Сохраняем данные в user_data
            if (!$this->saveUserData($user, $userData)) {
                DB::rollBack();
                return ['success' => false, 'message' => 'Failed to update user data', 'status_code' => 404];
            }

            // Фиксация транзакции
            DB::commit();
            return ['success' => true, 'message' => 'User updated successfully!', 'status_code' => 200];
        }
        catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'An error occurred during user update: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Мягкое удаление по ID.
     * @param $currentUser
     * @return array
     */
    public function deleteUser($currentUser, User $user): array {
        // Проверяем роль текущего пользователя
        if (!$currentUser->hasRole('admin')) {
            return ['success' => false, 'message' => "You do not have permission to delete users.", 'status_code' => 403];
        }

        // Проверяем, что пользователь не пытается удалить себя
        if ($user->id === $currentUser->id) {
            return ['success' => false, 'message' => 'You cannot delete yourself.', 'status_code' => 403];
        }

        // Мягкое удаление пользователя
        $user->delete();

        return ['success' => true, 'message' => "User successfully deleted.", 'status_code' => 200];
    }

    /**
     * Обновление пароля юзера админом
     * @param User $currentUser - Текущий авторизованный пользователь
     * @param array $validatedData - Валидированные данные
     * @return array
     */
    public function updatePassword(User $currentUser, array $validatedData): array {
        // Проверяем роль текущего пользователя
        if (!$currentUser->hasRole('admin')) {
            return [
                'success' => false,
                'message' => "You do not have permission to update passwords.",
                'status_code' => 403
            ];
        }

        // Получаем данные
        $userId = $validatedData['user_id'];
        $newPassword = $validatedData['new_password'];

        // Находим пользователя по ID
        $user = User::find($userId);

        // Проверяем, что пользователь найден
        if (!$user) {
            return [
                'success' => false,
                'message' => "User not found.",
                'status_code' => 404
            ];
        }

        // Обновляем пароль пользователя, используя bcrypt для хэширования
        $user->password = bcrypt($newPassword);
        $user->save();

        return [
            'success' => true,
            'message' => "Password successfully updated.",
            'status_code' => 200
        ];
    }

    /**
     * Получает пользователей по указанному полю и значению.
     *
     * @param string $field Поле для фильтрации.
     * @param string $value Значение для фильтрации.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByField(string $field, string $value) {
        $query = $this->baseQuery();

        // Определяем, к какой таблице относится поле и добавляем условие фильтрации
        if (in_array($field, [
            'company_name', 'surname', 'phone', 'name_invoice_recipient',
            'company_number', 'email_invoice_recipient', 'reference_number', 'c_o', 'post_address',
            'postcode', 'phone_2', 'municipality_number', 'kommune', 'country', 'number_country', 'group'
        ])) {
            // Поле находится в таблице user_data
            $query->where('user_data.' . $field, $value);
        }
        elseif ($field === 'role') {
            // Поле находится в таблице roles
            $query->where('roles.name', $value);
        }
        else {
            // Поле находится в таблице users
            $query->where('users.' . $field, $value);
        }

        // Выбираем необходимые поля
        $this->selectFields($query);

        // Выполняем запрос и возвращаем результат
        return $query->get();
    }

    /**
     * Подготавливает данные пользователя
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @param bool $isCreating Флаг, указывающий, создаем ли мы нового пользователя.
     * @return array Данные пользователя.
     */
    private function prepareUserData(array $validated, bool $isCreating = true): array {
        $userData = [
            'email' => $validated['email'],
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'name_invoice_recipient' => $validated['name_invoice_recipient'],
            'email_invoice_recipient' => $validated['email_invoice_recipient'],
            'company_name' => $validated['company_name'],
            'company_number' => $validated['company_number'],
            'c_o' => $validated['c_o'],
            'post_address' => $validated['post_address'],
            'postcode' => $validated['postcode'],
            'phone' => $validated['phone'],
            'phone_2' => $validated['phone_2'],
            'municipality_number' => $validated['municipality_number'],
            'kommune' => $validated['kommune'],
            'country' => $validated['country'],
            'number_country' => $validated['number_country'],
            'group' => $validated['group'],
            'reference_number' => $validated['reference_number'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'category_ids' => $validated['category_ids'],
        ];

        // Добавляем пароль только при создании
        if ($isCreating) {
            $userData['password'] = $validated['password'];
        }

        return $userData;
    }

    /**
     * Обрабатывает роль пользователя
     *
     * @param User $user
     * @param string $roleName
     * @return bool
     */
    private function handleUserRole(User $user, string $roleName): bool {
        // Ищем роль по имени
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            return false;
        }

        // Назначаем или обновляем роль пользователя
        $user->roles()->sync([$role->id]);

        return true;
    }

    /**
     * Создает или обновляет запись в user_data
     *
     * @param User $user
     * @param array $userData
     * @return bool
     */
    private function saveUserData(User $user, array $userData): bool {
        // Фильтруем данные для таблицы user_data, исключая данные, относящиеся к таблице users
        $userDataForUserData = array_merge(
            // Добавляем ID пользователя, чтобы связать user_data
            ['user_id' => $user->id],
            array_filter($userData, function ($key) {
                // Исключаем поля для таблицы users
                return !in_array($key, [
                    'name',
                    'email',
                    'password',
                    'role',
                    'status',
                ]);
            }, ARRAY_FILTER_USE_KEY)
        );

        // Используем updateOrCreate для обновления или создания записи в user_data
        $userDataEntry = UserData::updateOrCreate(['user_id' => $user->id], $userDataForUserData);

        return $userDataEntry ? true : false; // Возвращаем true при успешной операции
    }

    /**
     * Применяет поиск-фильт по указанному полю.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Запрос к базе данных.
     * @param string $field Поле для фильтрации.
     * @param string $inputValue Значение для фильтрации.
     * @return \Illuminate\Database\Eloquent\Builder Обновленный запрос.
     */
    private function applySearchFilter(Builder $query, $field, $inputValue): Builder {
        if (!empty($field) && !empty($inputValue)) {
            // Разделяем поисковую строку на отдельные термины
            $searchTerms = explode(' ', strtolower($inputValue));

            // Фильтрация по каждому поисковому термину
            $query->where(function ($query) use ($field, $searchTerms) {
                foreach ($searchTerms as $term) {
                    $term = trim($term);
                    if (!empty($term)) {
                        switch ($field) {
                            case 'company_name':
                                // Фильтрация по полю 'company_name' в таблице 'user_data'
                                $query->whereRaw('LOWER(user_data.company_name) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'role':
                                // Фильтрация по полю 'name' в таблице 'roles'
                                $query->whereRaw('LOWER(roles.name) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'email':
                                // Фильтрация по полю 'email' в основной таблице 'users'
                                $query->whereRaw('LOWER(users.email) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'phone':
                                // Фильтрация по полю 'phone' в таблице 'user_data'
                                $query->whereRaw('LOWER(user_data.phone) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'address':
                                // Фильтрация по полю 'post_address' в таблице 'user_data'
                                $query->whereRaw('LOWER(user_data.post_address) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'status':
                                // Фильтрация по полю 'status' в таблице 'users'
                                $query->whereRaw('LOWER(users.status) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'name':
                                // Фильтрация по полю 'name' в основной таблице 'users'
                                $query->whereRaw('LOWER(users.name) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'all':
                                // Если поле 'all', ищем по всем полям
                                $query->where(function($query) use ($term) {
                                    $query->whereRaw('LOWER(user_data.company_name) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(roles.name) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(users.email) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(user_data.phone) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(user_data.post_address) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(users.status) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(users.name) LIKE ?', ["%{$term}%"]);
                                });
                                break;

                            default:
                                break;
                        }
                    }
                }
            });
        }

        return $query;
    }

    /**
     * Формирует базовый запрос для пользователей.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function baseQuery(): Builder {
        $query = User::query();

        // Присоединяем связанные таблицы
        $query->leftJoin('user_data', 'users.id', '=', 'user_data.user_id');
        $query->leftJoin('role_user', 'users.id', '=', 'role_user.user_id');
        $query->leftJoin('roles', 'role_user.role_id', '=', 'roles.id');

        return $query;
    }

    /**
     * Выбирает необходимые поля для запроса.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function selectFields(Builder $query): void {
        $query->select(
            'users.*',
            'user_data.company_name',
            'user_data.surname',
            'user_data.phone',
            'user_data.name_invoice_recipient',
            'user_data.company_number',
            'user_data.email_invoice_recipient',
            'user_data.reference_number',
            'user_data.c_o',
            'user_data.post_address',
            'user_data.postcode',
            'user_data.phone_2',
            'user_data.municipality_number',
            'user_data.kommune',
            'user_data.country',
            'user_data.number_country',
            'user_data.group',
            'user_data.category_ids',
            'roles.name as role',
        );
    }
}
