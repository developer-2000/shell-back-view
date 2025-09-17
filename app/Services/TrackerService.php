<?php

namespace App\Services;


use App\Models\DistributorTracker;
use App\Models\PrintPromotionReport;
use App\Models\SystemSetting;
use App\Models\Test;
use App\Models\User;

class TrackerService {

    protected $promotion_id;
    protected $xlService;


    public function __construct($promotion_id) {
        $this->promotion_id = $promotion_id;

        $arrData = [
            'promotion_id' => $promotion_id,
            'promotion_name' => "",
            'display_address' => true,
            'display_categories' => false,
            'number_percent' => $this->getPercentPromotionReport(),
        ];

        $this->xlService = new XlFileService($arrData);
        $this->xlService->generateXLDate();
    }

    /**
     * Взять процент этой Report promotion
     *
     * @return int
     */
    public function getPercentPromotionReport(): int {
        // Взять проценты по умолчанию для каждого Report promotion
        $systemSetting = SystemSetting::first();
        $percent = $systemSetting ? $systemSetting->percent_promotion_report : 0;
        // Если CM сгенерировал Report для этого Promotion
        $promotionReport = PrintPromotionReport::where("promotion_id", $this->promotion_id)
            ->first();

        if ($promotionReport) {
            // Взять percent из CM данных
            $percent = $promotionReport->percent;
        }

        return $percent;
    }

    /**
     * Данные юзеров XL файла
     *
     * @return mixed
     */
    public function getXlUsersData(): mixed {
        return $this->xlService->getXlDateArray();
    }

    /**
     * Создать массив users (Компаний) с общим кол-во их поверхностей и уже отправленных дистрибьютером
     *
     * @return array
     */
    public function getArrUsersWithCountSurfaces(): array {
        $xlUsers = $this->getXlUsersData();
        $result = [];

        // Сбор всех имен пользователей из массива $xlUsers
        $userNames = array_map(function ($userData) {
            return $userData['Station'];
        }, $xlUsers->toArray());

        // Получаем всех пользователей из таблицы Users, у которых name в массиве $userNames
        $users = User::whereIn('name', $userNames)->get();

        // Получаем посылки дистрибьютера по promotion_id
        $trackers = DistributorTracker::where("promotion_id", $this->promotion_id)->get();

        // 1 Внести тех users кому были отправлены посылки от дистрибьютера
        foreach ($trackers as $tracker) {
            // Находим пользователя по company_id
            $user = $users->firstWhere('id', $tracker->company_id);

            // Считаем total_surfaces для пользователя из $xlUsers
            if ($user) {
                $userData = collect($xlUsers)->firstWhere('Station', $user->name);
                $filteredProperties = [];

                if ($userData) {
                    foreach ($userData as $key => $value) {
                        if (strpos($key, '-') !== false && (int) $value > 0) {
                            $filteredProperties[] = $key; // Сохраняем свойства с дефисом и значением > 0
                        }
                    }
                }

                // Добавляем в результат
                $result[] = [
                    'id' => $tracker->id,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'sent_surfaces' => count($tracker->sent_surfaces),
                    'total_surfaces' => count($filteredProperties),
                    'tracker_number' => $tracker->tracker_number,
                ];
            }
        }

        // 2 Добавить тех users кого небыло в посылках
        foreach ($xlUsers as $userData) {
            $userName = $userData['Station'];

            // Проверяем, есть ли пользователь в $result
            $user = $users->firstWhere('name', $userName);
            $alreadyInResult = collect($result)->contains('user_id', $user->id);

            if (!$alreadyInResult && $user) {
                $totalSurfaces = 0;

                // Считаем total_surfaces для пользователя
                foreach ($userData as $key => $value) {
                    if (strpos($key, '-') !== false && (int) $value > 0) {
                        $totalSurfaces += (int) $value;
                    }
                }

                $result[] = [
                    'id' => null,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'sent_surfaces' => 0,
                    'total_surfaces' => $totalSurfaces,
                    'tracker_number' => null,
                ];
            }
        }

        return $result;
    }

    /**
     * Проверка отправки дистрибьютером всем пользователям всех поверхностей
     *
     * @return bool
     */
    public function checkSendSurfacesFromDistributorToUsers(): bool {
        // Посылки users
        $usersTrackers = $this->getArrUsersWithCountSurfaces();

        // Суммируем sent_surfaces для каждого user_id
        $userSurfaceSummary = [];
        foreach ($usersTrackers as $tracker) {
            $userId = $tracker['user_id'];
            if (!isset($userSurfaceSummary[$userId])) {
                $userSurfaceSummary[$userId] = [
                    "user_id" => $userId,
                    "sent_surfaces" => 0,
                    "total_surfaces" => $tracker['total_surfaces']
                ];
            }
            $userSurfaceSummary[$userId]['sent_surfaces'] += $tracker['sent_surfaces'];
        }

        // Преобразуем в массив объектов
        $userSurfaceSummary = array_values($userSurfaceSummary);

        // Проверяем, равен ли итоговый sent_surfaces total_surfaces для каждого пользователя
        $allUsersValid = true;
        foreach ($userSurfaceSummary as $userSummary) {
            if ($userSummary['sent_surfaces'] < $userSummary['total_surfaces']) {
                $allUsersValid = false;
                break;
            }
        }

        return $allUsersValid;
    }

    // Проверка отправки от Printers всех их Surfaces к Distributor


}
