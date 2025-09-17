<?php

namespace App\Repositories;

use App\Models\SystemSetting;
use App\Models\User;

class SystemSettingRepository extends BaseRepository {

    public function getSettings(): array {
        // Получаем первую запись из таблицы system_settings
        $settings = SystemSetting::first();

        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        $distributors = User::whereHas('roles', function ($query) {
            $query->where('name', 'distributor');
        })->get();

        return [
            "settings" => $settings ? $settings->toArray() : [],
            "admins" => $admins,
            "distributors" => $distributors,
        ];
    }

    public function updateSystem(array $validated): array {
        try {
            $systemSetting = SystemSetting::updateOrCreate(
                ['id' => $validated['id']],
                [
                    'distributor_id' => $validated['distributor_id'] ?? null,
                    'admin_id' => $validated['admin_id'] ?? null,
                    'percent_promotion_report' => $validated['percent_promotion_report'],
                ]
            );

            // Если запись успешно обновлена или создана
            return [
                'success' => true,
                'message' => $validated['id'] ? 'System setting successfully updated.' : 'System setting successfully created.',
                'status_code' => 200,
                'data' => $systemSetting->toArray(),
            ];
        } catch (\Exception $e) {
            // Обработка ошибок
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

}
