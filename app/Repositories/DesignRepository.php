<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Design;
use Illuminate\Database\Eloquent\Builder;
use Exception;

class DesignRepository extends BaseRepository {

    /**
     * Выбирает дизайны в пагинации
     * @param array $params
     * @return array
     */
    public function getDesigns(array $params): array {

        $query = $this->baseQuery();

        // Выбираем необходимые поля
        $this->selectFields($query);

        // 1 Применяем поиск в указанных полях
        $query = $this->applySearchFilter($query, $params['field'], $params['input_value']);

        // 2 Применяем фильтрацию по категории
        if (!empty($params['only_category'])) {
            $query->where('category_id', '=', $params['only_category']);
        }

        // 3 Применяем сортировку по sort_by и sort_count
        $sortableFields = [
            'name' => 'name',
            'category' => 'category',
        ];
        $this->applySorting($query, $params, $sortableFields, 'name');

        // Получаем общее количество записей
        $total = $query->count();
        $categories = Category::select('id', 'name')->get();
        // Получаем отсортированные и постраничные данные
        $designs = $query->skip($params['start_index'])->take($params['count_show'])->get();

        return [
            'designs' => $designs,
            'total' => $total,
            'arr_categories' => $categories
        ];
    }

    /**
     * Создает новый дизайн
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом создания.
     */
    public function createDesign(array $validated): array {
        try {
            // 1 Подготавливаем данные для обновления
            $designData = $this->prepareDesignData($validated);

            // 2 Создаем
            $design = Design::create($designData);

            if ($design) {
                return ['success' => true, 'message' => 'Design saved successfully!', 'status_code' => 201];
            }
            else {
                return ['success' => false, 'message' => 'Failed to create design.', 'status_code' => 500];
            }
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Обновление дизайна.
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом обновления.
     */
    public function updateDesign(array $validated, Design $design): array {
        try {
            // 1 Подготавливаем данные для обновления
            $designData = $this->prepareDesignData($validated);

            // 2 Обновляем в базе
            $updateSuccess = $design->update($designData);

            // Проверка на успешное обновление
            if ($updateSuccess) {
                return [
                    'success' => true,
                    'message' => 'Design updated successfully!',
                    'status_code' => 200
                ];
            }
            else {
                return [
                    'success' => false,
                    'message' => 'Failed to update design.',
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
    public function deleteDesign($currentUser, Design $design): array {
        // Проверяем роль текущего пользователя
        if (!$currentUser->hasRole('admin') && !$currentUser->hasRole('cm-admin')) {
            return ['success' => false, 'message' => "You do not have permission to delete users.", 'status_code' => 403];
        }

        // Мягкое удаление
        $design->delete();

        return ['success' => true, 'message' => "Design successfully deleted.", 'status_code' => 200];
    }

    /**
     * Подготавливает данные дизайна
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Данные поверхности.
     */
    private function prepareDesignData(array $validated): array {
        $surfaceData = [
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
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
                            case 'name':
                                $query->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'all':
                                // Поиск по всем полям
                                $query->where(function($query) use ($term) {
                                    $query->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
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
        $query = Design::query();

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
            'id',
            'name',
            'category_id',
        );
    }
}
