<?php

namespace App\Repositories;

use App\Models\CompanyPlanner;
use App\Models\LogCompanyPlanner;
use App\Models\ReLogin;
use App\Models\Surface;
use App\Models\TypeSurface;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;


class CompanyPlannerRepository extends BaseRepository {

    /**
     * Выбрать все Surfaces с их категориями в пагинации и фильтрами
     * @param array $params
     * @param User $currentUser
     * @return array
     */
    public function getSurfaces(array $params, User $currentUser): array {
        $query = Surface::query();

        $query->select(
            'surfaces.id',
            'surfaces.vendor_code',
            'surfaces.name',
            'surfaces.description',
            'surfaces.status',
            'surfaces.url_images',
            'surfaces.type_surface',
            'surfaces.size_surface',
        );

        // 1 Применяем поиск в указанных полях
        $query = $this->applySearchFilter($query, $params['field'], $params['input_value']);

        // Выбираем данные из таблицы CompanyPlanner для текущего пользователя
        $companyPlanner = CompanyPlanner::where('user_id', $currentUser->id)->first();

        // 2 Применяем фильтрацию по only_quantity
        if ($params['only_quantity'] && $companyPlanner) {
            $surfaceIds = [];

            // Перебираем все элементы массива surfaces из CompanyPlanner
            foreach ($companyPlanner->surfaces as $surface) {
                // Разделяем строку по символу "_", и берем первую часть (ID)
                $surfaceId = explode('_', $surface)[0];

                // Добавляем найденный ID в массив
                $surfaceIds[] = $surfaceId;
            }

            // Добавляем условие в запрос для фильтрации по извлеченным surfaceIds
            $query->whereIn('surfaces.id', $surfaceIds);
        }

        $sortableFields = [
            'name' => 'surfaces.name',
            'vendor_code' => 'surfaces.vendor_code',
            'type_surface' => 'surfaces.type_surface',
            'size_surface' => 'surfaces.size_surface',
        ];

        // 3 Применяем сортировку по sort_by и sort_count
        $this->applySorting($query, $params, $sortableFields, 'surfaces.name');

        // Получаем общее количество записей
        $total = $query->count();

        // Выборка в пагинации
        $surfaces = $query->skip($params['start_index'])
            ->take($params['count_show'])
            ->select('id',
                'name',
                'vendor_code',
                'type_surface',
                'size_surface',
                'description',
                'url_images'
            )
            ->get();

        // Выбрать все типы поверхностей
        $typesSurface = TypeSurface::pluck('title');

        // 4 Данные поверхностей этого юзера
        $userSurfacesData = [];
        if ($companyPlanner && isset($companyPlanner->surfaces)) {
            foreach ($companyPlanner->surfaces as $surfaceString) {
                // Разделяем строку surface_id_amount на составляющие
                list($surfaceId, $amount) = explode('_', $surfaceString);

                // Добавляем кастомные объекты в массив
                $userSurfacesData[] = [
                    'surface' => (int) $surfaceId,
                    'amount' => (int) $amount,
                    'change' => false
                ];
            }
        }

        return [
            'surfaces' => $surfaces,
            'userSurfacesData' => $userSurfacesData,
            'total' => $total,
            'types_surface' => $typesSurface,
            ];
    }

    /**
     * Изменить Amount у категории поверхности
     * @param array $params
     * @param User $currentUser
     * @return array
     */
    public function saveAmountCompanyPlanner(array $params, User $currentUser): array {
        // Получаем запись CompanyPlanner по user_id
        $companyPlanner = CompanyPlanner::firstOrCreate(
            ['user_id' => $currentUser->id],
            ['surfaces' => []]
        );

        // Создаем новое значение в формате sur_id_amount
        $newValue = $params['surface_id'] . '_' . $params['amount'];
        $arrSurfaces = $companyPlanner->surfaces;
        $oldValue = 0;

        // Функция для поиска старого значения по префиксу
        $foundIndex = null;
        $prefix = $params['surface_id'] . '_';
        foreach ($arrSurfaces as $index => $value) {
            if (str_starts_with($value, $prefix)) {
                $foundIndex = $index;
                // Извлекаем старое значение amount после символа '_'
                $oldValue = (int)substr($value, strlen($prefix));
                break;
            }
        }

        // Если amount == 0 - удаляем запись из surfaces
        if ($params['amount'] == 0) {
            // Если найдено значение с этим префиксом, удаляем его
            if ($foundIndex !== null) {
                $arrSurfaces = array_filter($arrSurfaces, function($value) use ($params) {
                    return strpos($value, $params['surface_id'] . '_') !== 0;
                });
                // Пересоздаем индекс массива, так как array_filter сбрасывает ключи
                $arrSurfaces = array_values($arrSurfaces);
            }
        }
        // Если amount !== 0 - добавить запись в surfaces
        else {
            // Если найдено значение с префиксом, заменяем его
            if ($foundIndex !== null) {
                $arrSurfaces[$foundIndex] = $newValue;
            }
            else {
                // Иначе добавляем новое значение
                $arrSurfaces[] = $newValue;
            }
        }

        // Сохраняем изменения в CompanyPlanner
        $companyPlanner->surfaces = $arrSurfaces;
        $companyPlanner->save();

        // Проверяем, был ли пользователь перелогинен
        $reLogin = ReLogin::where("to_user_id", $currentUser->id)->first();
        // Это id user (Компания)
        $userId = $currentUser->id;
        if($reLogin){
            // Это id admin который перелогинился в user
            $userId = $reLogin->from_user_id;
        }

        // Логируем изменение или создание записи
        LogCompanyPlanner::create([
            "user_id" => $userId,
            "surface_id" => $params['surface_id'],
            "old_value" => $oldValue,
            "new_value" => $params['amount'],
        ]);

        return [
            'success' => true,
            'message' => 'New surface amount value saved successfully',
            'status_code' => 201
        ];
    }

    /**
     * Применяет фильтрацию по указанному полю.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Запрос к базе данных.
     * @param string $field Поле для фильтрации.
     * @param string $inputValue Значение для фильтрации.
     * @return \Illuminate\Database\Eloquent\Builder Обновленный запрос.
     */
    private function applySearchFilter(Builder $query, string $field, string $inputValue): Builder {
        if (!empty($field) && !empty($inputValue)) {
            // Разделяем поисковую строку на отдельные термины
            $searchTerms = explode(' ', strtolower($inputValue));

            // Фильтрация по каждому поисковому термину
            $query->where(function ($query) use ($field, $searchTerms) {
                foreach ($searchTerms as $term) {
                    $term = trim($term);
                    if (!empty($term)) {
                        switch ($field) {
                            case 'vendor_code':
                                $query->whereRaw('LOWER(surfaces.vendor_code) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'name':
                                $query->whereRaw('LOWER(surfaces.name) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'type_surface':
                                $query->whereRaw('LOWER(surfaces.type_surface) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'size_surface':
                                $query->whereRaw('LOWER(surfaces.size_surface) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'all':
                                // Если поле 'all', ищем по всем полям
                                $query->where(function($query) use ($term) {
                                    $query->whereRaw('LOWER(surfaces.vendor_code) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(surfaces.name) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(surfaces.type_surface) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(surfaces.size_surface) LIKE ?', ["%{$term}%"]);
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

}

