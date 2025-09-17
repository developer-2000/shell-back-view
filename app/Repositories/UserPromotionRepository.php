<?php

namespace App\Repositories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Builder;
use Exception;
use Illuminate\Support\Facades\Schema;

class UserPromotionRepository extends BaseRepository {

    /**
     * Выбирает promotions в пагинации
     * @param array $params
     * @return array
     */
    public function getPromotions(array $params): array {

        $query = $this->baseQuery();

        // Выбрать только разрешенные к просмотру
        $query->where('show_in_user_promotions', true);

        // Выбираем необходимые поля
        $this->selectFields($query);

        // 1 Применяем поиск в указанных полях
        $query = $this->applySearchFilter($query, $params['field'], $params['input_value']);

        // 2 Применяем фильтрацию по статусу, если only_active == true
        if (!empty($params['only_active']) && $params['only_active']) {
            $query->where('status', true);
        }

        // 3 Применяем фильтрацию по диапазону дат, если date_picker не пуст
        if (!empty($params['date_picker'])) {
            $dateFrom = $params['date_picker']['from'];
            $dateTo = $params['date_picker']['to'];

            // Фильтрация по пересечению диапазонов дат
            $query->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereRaw(
                    "( JSON_UNQUOTE(JSON_EXTRACT(period, '$.from')) >= ? AND JSON_UNQUOTE(JSON_EXTRACT(period, '$.from')) < ? ) AND
                ( JSON_UNQUOTE(JSON_EXTRACT(period, '$.to')) > ? AND JSON_UNQUOTE(JSON_EXTRACT(period, '$.to')) <= ? )",
                    [$dateFrom, $dateTo, $dateFrom, $dateTo]
                );
            });
        }

        // 4 Применяем сортировку по sort_by и sort_count
        $sortableFields = [
            'name' => 'name',
            'created_at' => 'created_at',
            'period' => 'period',
            'status' => 'status',
        ];
        $this->applySorting($query, $params, $sortableFields, 'name');

        // Получаем общее количество записей
        $total = $query->count();

        // Получаем отсортированные и постраничные данные
        $promotions = $query->skip($params['start_index'])
            ->select("id",
                "url_images",
                "name",
                "created_at",
                "period",
                "status",
                "description",
                "surfaces",
            )
            ->take($params['count_show'])
            ->get();

        return [
            'promotions' => $promotions,
            'total' => $total,
            'config_promotion_status' => config("site.promotion.status"),
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
                            case 'name':
                                $query->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'all':
                                // Если поле 'all', ищем по всем полям
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
     * Формирует базовый запрос.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function baseQuery(): Builder {
        $query = Promotion::query();

        return $query;
    }

    /**
     * Выбирает необходимые поля для запроса.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function selectFields(Builder $query): void {
        // Получаем все столбцы таблицы
        $columns = Schema::getColumnListing($query->from);

        // Исключаем столбцы 'updated_at' и 'deleted_at'
        $columns = array_diff($columns, ['updated_at', 'deleted_at']);

        // Применяем выборку столбцов к запросу
        $query->select($columns);
    }
}
