<?php

namespace App\Repositories;

use App\Jobs\SentCmAndAdminAboutDistributorJob;
use App\Jobs\SentUserAboutTrackerDistributorJob;
use App\Models\DistributorTracker;
use App\Models\PrintedPromotions;
use App\Models\Promotion;
use App\Models\SystemSetting;
use App\Models\Test;
use App\Models\User;
use App\Services\TrackerService;
use Illuminate\Database\Eloquent\Builder;

class DistributorRepository {

    /**
     * Выборка всех Promotions по таблице PrintedPromotions
     *
     * @param array $params
     * @return array
     */
    public function getPromotions(array $params): array {
        // выбрать без повторов promotion_id
        $query = PrintedPromotions::with(['promotion', 'printer'])
            ->select('printed_promotions.*') // Выбираем все столбцы
            ->joinSub(
                PrintedPromotions::selectRaw('MIN(id) as id') // Берем минимальный ID для каждого promotion_id
                ->groupBy('promotion_id'),
                'first_promotions',
                'printed_promotions.id',
                '=',
                'first_promotions.id'
            );

        // 1 Применяем поиск в указанных полях
        $query = $this->applySearchFilter($query, $params['field'], $params['input_value']);
        // 2 Применяем фильтрацию по статусу, если only_active == true
        if (!empty($params['only_active']) && $params['only_active']) {
            $query->whereHas('promotion', function($query) {
                // Фильтрация по статусу в модели Promotion
                $query->where('status', true);
            });
        }
        // 3 Применяем фильтрацию по диапазону дат, если date_picker не пуст
        if (!empty($params['date_picker'])) {
            $dateFrom = $params['date_picker']['from'];
            $dateTo = $params['date_picker']['to'];

            // Фильтрация по пересечению диапазонов дат
            $query->whereHas('promotion', function ($q) use ($dateFrom, $dateTo) {
                $q->whereRaw(
                    "( JSON_UNQUOTE(JSON_EXTRACT(period, '$.from')) >= ? AND JSON_UNQUOTE(JSON_EXTRACT(period, '$.from')) < ? ) AND
                 ( JSON_UNQUOTE(JSON_EXTRACT(period, '$.to')) > ? AND JSON_UNQUOTE(JSON_EXTRACT(period, '$.to')) <= ? )",
                    [$dateFrom, $dateTo, $dateFrom, $dateTo]
                );
            });
        }
        // 4 Применяем сортировку по sort_by и sort_count
        $sortableFields = [
            'name' => 'promotions.name',
            'created_at' => 'promotions.created_at',
            'period' => 'promotions.period',
            'status' => 'promotions.status',
        ];

        // Выполняем join с таблицей promotions для сортировки по её полям
        $query->join('promotions', 'promotions.id', '=', 'printed_promotions.promotion_id');

        $this->applySorting($query, $params, $sortableFields, 'promotions.name');

        // Получаем общее количество записей
        $total = $query->count();
        // Получаем отсортированные и постраничные данные
        $promotions = $query->skip($params['start_index'])->take($params['count_show'])->get();

        return [
            'promotions' => $promotions,
            'total' => $total,
            'config_promotion_status' => config("site.promotion.status"),
        ];
    }

    /**
     * Установить Трекер номера Дистрибьютера
     *
     * @param array $validated
     * @return array
     */
    public function setDistributorTracker(array $validated): array {
        $promotion = Promotion::find($validated["promotion_id"]);

        // Создание новой записи в таблице distributor_trackers
        $tracker = DistributorTracker::create([
            'promotion_id' => $validated['promotion_id'],
            'company_id' => $validated['company_id'],
            'tracker_number' => $validated['tracker_number'],
            'sent_surfaces' => $validated['sent_surfaces'],
            'description' => $validated['description'] ?? null,
        ]);

        // 1 Отправить письмо User (Компания)
        // Выбрать все surface-design названия
        $surfaceNames = array_map(function ($surface) {
            return $surface['name'];
        }, $validated['sent_surfaces']);
        // Выбрать user (Компания)
        $user = User::where("id",$validated['company_id'])->first();
        if($user){
            dispatch(new SentUserAboutTrackerDistributorJob(
                $user,
                $promotion->name,
                $validated['tracker_number'],
                $surfaceNames
            ));
        }

        // 2 Отправка писем CM создавшему Promotion и Admin отвечающий за Promotions
        // Если Tracker отправлен по всем Surfaces и всем users (Компаниям)
        $trackService = new TrackerService($validated['promotion_id']);

        // Проверка отправки дистрибьютером всем пользователям всех поверхностей
        if($trackService->checkSendSurfacesFromDistributorToUsers()){
            // 1. Отправить CM и Admin Email об завершении отправок от Distributor
            $ids = [];
            $settings = SystemSetting::first();

            // id создавшего promotion
            if($promotion->who_created_id){
                $ids[] = $promotion->who_created_id;
            }
            // id admin отвечающий за promotions
            if($settings && $settings->admin_id && $settings->admin_id !== $promotion->who_created_id){
                $ids[] = $settings->admin_id;
            }

            $getXlUsersData = $trackService->getXlUsersData() ?: collect();
            // Все имена users (Компании) участвующих в этой Promotion
            $userNames = array_column($getXlUsersData->toArray(), 'Station');

            if (!empty($ids)) {
                $users = User::whereIn('id', $ids)->get();

                if ($users->isNotEmpty()) {
                    dispatch(new SentCmAndAdminAboutDistributorJob(
                        $users,
                        $promotion->name,
                        $userNames
                    ));
                }
            }

            // 2. Поменять статус Promotion на 4
            $promotion = Promotion::find($validated['promotion_id']);
            $promotion->update([
                'complete_distributor_work' => now(),
                'status' => Promotion::STATUS_SENT_BY_THE_DISTRIBUTOR
            ]);
        }

        return [
            'success' => true,
            'message' => 'The letter about the shipment has been sent to the company.',
            'status_code' => 200
        ];
    }

    // Сортировка таблицы по указанному или default полю
    private function applySorting(
        $query,
        array $params,
        array $sortableFields,
        string $defaultSortField = 'id'): void {

        $direction = null;

        if (!empty($params['sort_by']) && isset($params['sort_count'])) {
            if ($params['sort_count'] == 1) {
                $direction = 'desc';
            }
            else if ($params['sort_count'] == 2) {
                $direction = 'asc';
            }

            if ($params['sort_by'] === 'period') {
                // Сортировка по полю 'to' внутри JSON, в модели promotion
                $query->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(promotions.period, '$.to')) AS DATETIME) {$direction}");
            }
            // остальные столбцы базы
            else if ($direction && isset($sortableFields[$params['sort_by']])) {
                $query->orderBy($sortableFields[$params['sort_by']], $direction);
            }
        }

        // Сортировка по умолчанию
        if (!$direction) {
            $query->orderBy($defaultSortField, 'desc');
        }
    }

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
                                // Ищем в поле 'name' из модели Promotion
                                $query->whereHas('promotion', function($query) use ($term) {
                                    $query->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
                                });
                                break;

                            case 'all':
                                // Если поле 'all', ищем по всем полям
                                $query->WhereHas('promotion', function($query) use ($term) {
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

}
