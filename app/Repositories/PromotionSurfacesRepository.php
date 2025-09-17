<?php

namespace App\Repositories;

use App\Models\Promotion;
use App\Models\PromotionSurface;
use Illuminate\Support\Facades\DB;


class PromotionSurfacesRepository extends BaseRepository {

    /**
     * Добавить surface в promotion
     * @param array $validated
     * @return array
     */
    public function addSurfaceInPromotion(array $validated): array {
        try {
            return $this->createSurfaceInPromotion($validated['promotion_id'], $validated['surface_id']);
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to add surface to promotion: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Удалить surface из promotion
     * @param int $promotion_id
     * @param int $surface_id
     * @return array
     */
    public function deleteSurfaceInPromotion(int $promotion_id, int $surface_id): array {
        try {
            // Выбрать связку
            $existingEntry = PromotionSurface::where('promotion_id', $promotion_id)
                ->where('surface_id', $surface_id)
                ->first();

            if (!$existingEntry) {
                return ['success' => false, 'message' => 'Surface not found in promotion', 'status_code' => 404];
            }

            // Удаление связки
            $response = $this->deleteSurfaceAtPromotion($existingEntry);

            return ['success' => true, 'message' => 'Surface successfully removed from promotion', 'status_code' => 200];
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to delete surface from promotion: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Выбрать все акции с определенными полями
     * @return array
     */
    public function getAllPromotions(): array {
        // Получаем все акции
        $allPromotions = Promotion::all();

        // Подготовим данные для вывода
        $correctedPromotions = $allPromotions->map(function ($promotion) {
            return [
                'id' => $promotion->id,
                'name' => $promotion->name,
            ];
        });

        return [
            'success' => true,
            'message' => '',
            'status_code' => 200,
            'data' => $correctedPromotions->toArray(),
        ];
    }

    /**
     * Поменять все surfaces у promotion
     * @param int $from_promotion_id
     * @param int $whom_promotion_id
     * @return array
     */
    public function changeSurfacesAtPromotion(int $from_promotion_id, int $whom_promotion_id): array {
        DB::beginTransaction(); // Начало транзакции

        try {
            // Удаляем все связки из первой акции
            $existingEntries = PromotionSurface::where('promotion_id', $from_promotion_id)->get();

            foreach ($existingEntries as $entry) {
                $response = $this->deleteSurfaceAtPromotion($entry);
                // Проверяем успешность удаления, если требуется
                if (!$response['success']) {
                    DB::rollBack(); // Откат транзакции в случае ошибки
                    return $response;
                }
            }

            // Получаем все surfaces из второй акции
            $whomPromotion = PromotionSurface::where('promotion_id', $whom_promotion_id)->get();

            // Добавляем все surfaces из первой акции в вторую акцию
            foreach ($whomPromotion as $whom) {
                $response = $this->createSurfaceInPromotion($from_promotion_id, $whom->surface_id);
                // Проверяем успешность добавления, если требуется
                if (!$response['success']) {
                    DB::rollBack(); // Откат транзакции в случае ошибки
                    return $response;
                }
            }

            DB::commit(); // Завершение транзакции
            return ['success' => true, 'message' => 'Surfaces successfully transferred to the new promotion.', 'status_code' => 200];
        }
        catch (\Exception $e) {
            DB::rollBack(); // Откат транзакции в случае ошибки
            return ['success' => false, 'message' => 'Failed to change surfaces at promotion: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Удалить Surface у Promotion
     * @param PromotionSurface $existingEntry
     * @return array
     */
    private function deleteSurfaceAtPromotion(PromotionSurface $existingEntry): array {
        // Мягкое удаление
        $existingEntry->delete();

        // Проверяем, что запись была удалена
        if ($existingEntry->trashed()) {
            return ['success' => true, 'message' => 'Surface successfully removed from promotion.', 'status_code' => 200];
        }

        return ['success' => false, 'message' => 'Failed to remove surface from promotion.', 'status_code' => 500];
    }

    /**
     * Создать Surface в Promotion
     * @param $promotionSurface
     * @param $promotionId
     * @param $surfaceId
     * @return array
     */
    private function createSurfaceInPromotion($promotionId, $surfaceId): array {
        // Выбрать связку
        $existingEntry = PromotionSurface::withTrashed()
            ->where('promotion_id', $promotionId)
            ->where('surface_id', $surfaceId)
            ->first();

        // Если запись была удалена, восстанавливаем её
        if ($existingEntry) {
            if ($existingEntry->trashed()) {
                $existingEntry->restore();
                return ['success' => true, 'message' => 'Surface successfully restored and added to promotion.', 'status_code' => 200];
            }

            // Если запись существует и не удалена
            return ['success' => false, 'message' => 'This surface is already assigned to the promotion.', 'status_code' => 422];
        }

        // Создание новой записи
        PromotionSurface::create([
            'promotion_id' => $promotionId,
            'surface_id' => $surfaceId,
            'designs' => [],
        ]);

        return ['success' => true, 'message' => 'Surface successfully added to promotion.', 'status_code' => 200];
    }

}
