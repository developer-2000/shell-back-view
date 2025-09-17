<?php

namespace App\Repositories;

use App\Models\CompanyPlanner;
use App\Models\SizeSurface;
use App\Models\Surface;
use App\Models\Test;
use App\Models\TypeSurface;
use Illuminate\Database\Eloquent\Builder;
use Exception;

class SurfaceRepository extends BaseRepository {

    public function getSurfaces(array $params): array {

        $query = $this->baseQuery();

        // Выбираем необходимые поля
        $this->selectFields($query);

        // 1 Применяем поиск в указанных полях
        $query = $this->applySearchFilter($query, $params['field'], $params['input_value']);

        // 2 Применяем сортировку по sort_by и sort_count
        $sortableFields = [
            'vendor_code' => 'surfaces.vendor_code',
            'name' => 'surfaces.name',
            'type_surface' => 'surfaces.type_surface',
            'size_surface' => 'surfaces.size_surface',
            'description' => 'surfaces.description',
            'divided_bool' => 'surfaces.divided_bool',
        ];

        // 3 Сортировка по полю
        $this->applySorting($query, $params, $sortableFields, 'surfaces.vendor_code');

        // Получаем общее количество записей
        $total = $query->count();
        // Получаем уникальные значения для type_surface и size_surface
        $typeSurfaces = TypeSurface::pluck('title');
        $sizeSurfaces = SizeSurface::pluck('title');
        // Получаем отсортированные и постраничные данные
        $surfaces = $query->skip($params['start_index'])->take($params['count_show'])->get();

        return [
            'surfaces' => $surfaces,
            'total' => $total,
            'arr_type_surfaces' => $typeSurfaces,
            'arr_size_surfaces' => $sizeSurfaces,
        ];
    }

    /**
     * Создает новую поверхность
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом создания.
     */
    public function createSurface(array $validated): array {
        try {
            // Подготавливаем данные для обновления
            $surfaceData = $this->prepareSurfaceData($validated);

            // Создаем новый surface в базе
            $surface = Surface::create($surfaceData);

            // Проверка на успешное создание
            if ($surface) {
                 return ['success' => true, 'message' => 'Surface saved successfully!', 'status_code' => 201];
            }
            else {
                return ['success' => false, 'message' => 'Failed to create surface.', 'status_code' => 500];
            }
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Обновление поверхности.
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом обновления.
     */
    public function updateSurface(array $validated, Surface $surface): array {
        try {
            // Подготавливаем данные для обновления
            $surfaceData = $this->prepareSurfaceData($validated);

            // Обновляем поверхность в базе
            $updateSuccess = $surface->update($surfaceData);

            // Проверка на успешное обновление
            if ($updateSuccess) {
                return [
                    'success' => true,
                    'message' => 'Surface updated successfully!',
                    'status_code' => 200
                ];
            }
            else {
                return [
                    'success' => false,
                    'message' => 'Failed to update surface.',
                    'status_code' => 500
                ];
            }
        }
        catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Мягкое удаление по ID.
     * @param $currentUser
     * @return array
     */
    public function deleteSurface($currentUser, Surface $surface): array {
        // Проверяем роль текущего пользователя
        if (!$currentUser->hasRole('admin')) {
            return ['success' => false, 'message' => "You do not have permission to delete users.", 'status_code' => 403];
        }

        // Мягкое удаление
        $surface->delete();

        return ['success' => true, 'message' => "Surface successfully deleted.", 'status_code' => 200];
    }

    /**
     * Создает новый тип поверхности
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом создания.
     */
    public function createTypeSurface(array $validated): array {
        try {
            // Создаем новую запись
            $typeSurface = TypeSurface::create([
                'title' => $validated['new_type'],
            ]);

            // Проверка на успешное создание
            if ($typeSurface) {
                return ['success' => true, 'message' => 'Type surface created successfully!', 'status_code' => 201];
            } else {
                return ['success' => false, 'message' => 'Failed to create type surface.', 'status_code' => 500];
            }
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Создает новый размер поверхности
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом создания.
     */
    public function createSizeSurface(array $validated): array {
        try {
            // Создаем новую запись
            $sizeSurface = SizeSurface::create([
                'title' => $validated['new_size'],
            ]);

            // Проверка на успешное создание
            if ($sizeSurface) {
                return ['success' => true, 'message' => 'Size surface created successfully!', 'status_code' => 201];
            } else {
                return ['success' => false, 'message' => 'Failed to create size surface.', 'status_code' => 500];
            }
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Клонирует surface и создает дубликаты в таблицах Surface и CompanyPlanner
     *
     * @param array $validated
     * @return array
     */
    public function createCloneSurface(array $validated): array {
        try {
            // Находим существующую запись по surface_id
            $existingSurface = Surface::find($validated['surface_id']);

            // Если запись не найдена, возвращаем ошибку
            if (!$existingSurface) {
                return [
                    'success' => false,
                    'message' => 'Surface not found.',
                    'status_code' => 404
                ];
            }

            // Создаем новый объект на основе существующего
            $clonedSurface = $existingSurface->replicate();

            // Обновляем поле name для клона
            $clonedSurface->name = $validated['name'];

            // 1 Сохраняем новую surface в базе
            $clonedSurface->save();

            if($validated['bool_clone']){
                // 2 Клонировать записи старой surface подставляя новый id в CompanyPlanners
                $this->addNewSurfaceInCompanyPlanners($validated['surface_id'], $clonedSurface->id);
            }

            return [
                'success' => true,
                'message' => 'Surface cloned successfully.',
                'status_code' => 201
            ];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Клонировать записи старой surface подставляя новый id в CompanyPlanners
     *
     * @param int $oldSurfaceId
     * @param $clonedSurface
     * @return void
     */
    private function addNewSurfaceInCompanyPlanners(int $oldSurfaceId, int $newId): void {
        $companyPlanners = CompanyPlanner::get();

        foreach ($companyPlanners as $companyPlanner) {
            $newSurfaces = [];

            foreach ($companyPlanner->surfaces as $surface) {
                // Разбиваем строку на части по символу "_"
                $parts = explode('_', $surface);
                // Первая часть — это ID поверхности
                $surfaceId = $parts[0];
                // Добавляем старую запись
                $newSurfaces[] = $surface;

                // Если ID соответствует старому surface
                if ($surfaceId == $oldSurfaceId) {
                    // Создаем новую запись
                    $newSurface = $newId . '_' . $parts[1];
                    $newSurfaces[] = $newSurface;
                }
            }

            // Обновляем массив surfaces с помощью setAttribute
            $companyPlanner->setAttribute('surfaces', $newSurfaces);

            // Сохраняем изменения в CompanyPlanner
            $companyPlanner->save();
        }
    }

    /**
     * Подготавливает данные поверхности
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Данные поверхности.
     */
    private function prepareSurfaceData(array $validated): array {
        $surfaceData = [
            'vendor_code' => $validated['vendor_code'] ?? 00000,
            'price' => $validated['price'] ?? 0,
            'name' => $validated['name'] ?? 'Unknown',
            'type_surface' => $validated['type_surface'] ?? null,
            'size_surface' => $validated['size_surface'] ?? null,
            'description' => $validated['description'] ?? null,
            'divided_bool' => $validated['divided_bool'] ?? false,
            // Преобразование значений в integer
            'status' => isset($validated['status']) ? array_map('intval', $validated['status']) : [],
            'printer_id' => is_numeric($validated['printer_id']) ? (int)$validated['printer_id'] : null,
        ];

        return $surfaceData;
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
                                $query->whereRaw('LOWER(type_surfaces.title) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'size_surface':
                                $query->whereRaw('LOWER(size_surfaces.title) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'description':
                                $query->whereRaw('LOWER(surfaces.description) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'all':
                                // Если поле 'all', ищем по всем полям
                                $query->where(function($query) use ($term) {
                                    $query->whereRaw('LOWER(surfaces.vendor_code) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(surfaces.name) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(type_surfaces.title) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(size_surfaces.title) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(surfaces.description) LIKE ?', ["%{$term}%"]);
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
     * Формирует базовый запрос
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function baseQuery(): Builder {
        $query = Surface::query();

        // Присоединяем связанные таблицы
        $query->leftJoin('type_surfaces', 'surfaces.type_surface', '=', 'type_surfaces.title');
        $query->leftJoin('size_surfaces', 'surfaces.size_surface', '=', 'size_surfaces.title');

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
            'surfaces.id',
            'surfaces.vendor_code',
            'surfaces.name',
            'surfaces.description',
            'surfaces.status',
            'surfaces.divided_bool',
            'surfaces.url_images',
            'surfaces.printer_id',
            'surfaces.price',
            'type_surfaces.title as type_surface',
            'size_surfaces.title as size_surface'
        );
    }
}
