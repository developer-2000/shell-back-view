<?php

namespace App\Repositories;

use App\Models\LogCompanyPlanner;
use Illuminate\Database\Eloquent\Builder;


class HistoryPlannerRepository extends BaseRepository {


    public function getHistory(array $params): array {
        // Инициализация запроса
        $query = $this->baseQuery();

        // 1 Выбираем необходимые поля
        $this->selectFields($query);

        // 2 Применяем сортировку по sort_by и sort_count
        $sortableFields = [
            'time' => 'log_company_planners.updated_at',
            'user_name' => 'users.name',
            'surface_name' => 'surfaces.name',
        ];

        // 3 Сортировка по полю
        $this->sortingRow($query, $params, $sortableFields, 'log_company_planners.updated_at');

        $total = $query->count();

        // Получаем отсортированные и постраничные данные
        $history = $query->skip($params['start_index'])
            ->take($params['count_show'])
            ->get();

        return [
            'history' => $history,
            'total' => $total,
        ];
    }

    private function selectFields(Builder $query): void {
        $query->select(
            'log_company_planners.id',
            'log_company_planners.user_id',
            'log_company_planners.surface_id',
            'log_company_planners.old_value',
            'log_company_planners.new_value',
            'log_company_planners.updated_at',
            'users.name as user_name',
            'surfaces.name as surface_name'
        );
    }

    private function baseQuery(): Builder {
        $query = LogCompanyPlanner::query();

        // Присоединяем связанные таблицы
        $query->leftJoin('users', 'log_company_planners.user_id', '=', 'users.id')
            ->leftJoin('surfaces', 'log_company_planners.surface_id', '=', 'surfaces.id');

        return $query;
    }

    protected function sortingRow( $query, array $params, array $sortableFields, string $defaultSortField): void {
        $direction = null;

        if (!empty($params['sort_by']) && isset($params['sort_count'])) {
            if ($params['sort_count'] == 1) {
                $direction = 'desc';
            } else if ($params['sort_count'] == 2) {
                $direction = 'asc';
            }

            if ($direction && isset($sortableFields[$params['sort_by']])) {
                $query->orderBy($sortableFields[$params['sort_by']], $direction);
            }
        }

        // Сортировка по умолчанию
        if (!$direction) {
            $query->orderBy($defaultSortField, 'desc');
        }
    }

}

