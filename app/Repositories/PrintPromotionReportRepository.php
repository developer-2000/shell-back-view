<?php

namespace App\Repositories;

use App\Jobs\SentDistributorAboutPrintedSurfacesJob;
use App\Models\DesignChat;
use App\Models\PrintedPromotions;
use App\Models\PrintPromotionReport;
use App\Models\Promotion;
use App\Models\PromotionSurfaceDesign;
use App\Models\SystemSetting;
use App\Models\Test;
use App\Models\User;
use App\Services\TrackerService;

class PrintPromotionReportRepository extends BaseRepository {

    /**
     * Выборка данных Report этого Printer для определенного Promotion
     *
     * @param array $validated
     * @param User $currentUser
     * @return array
     */
    public function getReport(array $validated, User $currentUser): array {
        $promotionData = Promotion::where('id', $validated['promotion_id'])
            ->whereNotNull("send_to_printer")
            ->first();

        $surfaceDesignData = PromotionSurfaceDesign::where('promotion_id', $validated['promotion_id'])
            ->with("surface","design")
            ->get();

        // Отобрать только Surfaces принадлежащие этому Printer
        $filteredData = $surfaceDesignData->filter(function ($item) use ($currentUser) {
            return $item->surface->printer_id == $currentUser->id;
        })->values();

        // 1 Подготовить Report данные для этого Printer
        [$aggregatedSurfaces, $percent] = $this->preparingReportArray( $validated['promotion_id'], $filteredData );
        // 2 Сформировать данные для Front
        $designsArr = $this->generateDataResponse($aggregatedSurfaces, $filteredData, $percent);
        // 3 Выбрать все отпечатанные этим Принтером поверхности
        $printed = PrintedPromotions::where("promotion_id", $validated['promotion_id'])
            ->where("printer_id", $currentUser->id)
            ->get();
        // 4 Выбрать данные Дистрибьютера
        $settings = SystemSetting::with("distributor")->first();
        $distributor = null;
        if($settings && !is_null($settings->distributor)){
            $distributor = $settings->distributor->merged_user_data;
            $distributor = [
                "name" => $distributor['surname'] ?? null,
                "phone" => $distributor['phone'] ?? null,
                "address" => $distributor['post_address'] ?? null,
            ];
        }

        $printReport = PrintPromotionReport::where("promotion_id", $validated['promotion_id'])->first();

        return [
            'success' => true,
            'message' => 'Promotion surfaces retrieved successfully.',
            'status_code' => 200,
            'data' => [
                'promotion' => [
                    'id' => $promotionData->id,
                    'name' => $promotionData->name,
                    'designs' => $designsArr
                ],
                'printed' => $printed,
                'distributor' => $distributor,
                'print_report' => $printReport,
            ]
        ];
    }

    /**
     * Отправить Tracker посылки Принтера с Дизайнами в ней
     *
     * @param array $validated
     * @param User $currentUser
     * @return array
     */
    public function setPrinted(array $validated, User $currentUser): array {
        // Получаем первую запись из таблицы system_settings
        $settings = SystemSetting::first();

        // 1 Отправка Email Дистрибьютеру
        if($settings && !is_null($settings->distributor_id)){
            $distributor = User::where("id", $settings->distributor_id)->first();
            if ($distributor) {
                dispatch(new SentDistributorAboutPrintedSurfacesJob(
                    $distributor,
                    $validated,
                    $currentUser->id
                ));
            }
        }
        else{
            return [
                'success' => true,
                'message' => 'The Distributor is not assigned in the system!',
                'status_code' => 200,
            ];
        }

        // 2 Сохранить Tracker посылки Принтера с Дизайнами в ней
        $print = PrintedPromotions::create( [
                'promotion_id' => $validated['promotion_id'],
                'printer_id' => $currentUser->id,
                'printer_tracker_number' => $validated['printer_tracker_number'],
                'sent_surfaces' => $validated['sent_surfaces'],
                'description' => $validated['description'],
            ] );

        // 3 Изменить статус Promotion если были отправлены все дизайны Принтерами
        $promotionReport = PrintPromotionReport::where("promotion_id", $validated['promotion_id'])
            ->first();
        // Собрать дизайны и посчитать amount   { "1 - 1": 8 }
        $aggregatedSurfaces = $this->collectDesignsCalculateAmount($promotionReport);

        $printerPromotion = PrintedPromotions::where("promotion_id", $validated['promotion_id'])
            ->get();

        // Перебираем все записи PrintedPromotions
        foreach ($printerPromotion as $printed) {
            $sentSurfaces = $printed->sent_surfaces;

            // Перебираем каждый объект в массиве sent_surfaces
            foreach ($sentSurfaces as $sentSurface) {
                $surfaceId = $sentSurface['surface']['id'];
                $designId = $sentSurface['design']['id'];
                $key = "$surfaceId - $designId";

                // Проверяем наличие ключа в $aggregatedSurfaces и удаляем его
                if (isset($aggregatedSurfaces[$key])) {
                    unset($aggregatedSurfaces[$key]);
                }
            }
        }

        // Массив пуст - все поверхности отправлены принтерами
        if (empty($aggregatedSurfaces)) {
            // Изменить статус на 3
            $promotion = Promotion::find($validated['promotion_id']);
            $promotion->update([
                'send_to_distributor' => now(),
                'status' => Promotion::STATUS_PRINTED_BY_THE_PRINTER
            ]);
        }

        return [
            'success' => true,
            'message' => 'Promotion printed successfully created.',
            'status_code' => 200,
        ];
    }

    /**
     * Сформировать данные для Front
     *
     * @param $aggregatedSurfaces
     * @param $filteredData
     * @param $percent
     * @return array
     */
    private function generateDataResponse($aggregatedSurfaces, $filteredData, $percent): array {
        $designsArr = [];

        // Перебор Ids Поверхностей с Дизайнами этого Printer
        foreach ($aggregatedSurfaces as $key => $value) {
            // Разделение ключа на surface_id и design_id
            [$surfaceId, $designId] = explode(' - ', $key);

            // Поиск объекта PromotionSurfaceDesign
            $matchedItem = $filteredData->first(function ($item) use ($surfaceId, $designId) {
                return $item->surface->id == $surfaceId && $item->design->id == $designId;
            });

            // Сформировать обьект для вывода на Front
            if ($matchedItem) {
                // Ищем чат с данным chat_id
                $chatObj = DesignChat::where("id", $matchedItem->chat_id)->first();


                $image = "";
                $document = "";
                if ($chatObj && isset($chatObj->messages)) {
                    foreach ($chatObj->messages as $message) {
                        // Ищем сообщение с type_file == "HQ"
                        if ($message['type_file'] == 'HQ') {
                            // Image
                            if ($message['type_extension'] == 'image' && !$message['delete_message']) {
                                $image = [
                                    "url" => $message['url_images']['w_200'],
                                    "chat_id" => $matchedItem->chat_id,
                                    "message_id" => $message['id'],
                                ];
                                break;
                            }
                            // Document
                            elseif ($message['type_extension'] == 'document' && !$message['delete_message']) {
                                $document = [
                                    "url" => $message['url_file'],
                                    "chat_id" => $matchedItem->chat_id,
                                    "message_id" => $message['id'],
                                ];
                                break;
                            }
                        }

                    }
                }

                // Расчет процентов
                $calculatedPercent = ceil(($value * $percent) / 100);
                // Расчет total (amount + percent)
                $total = $value + $calculatedPercent;

                $designsArr[] = [
                    "surface" => [
                        "id" => $matchedItem->surface->id,
                        "name" => $matchedItem->surface->name,
                    ],
                    "design" => [
                        "id" => $matchedItem->design->id,
                        "name" => $matchedItem->design->name,
                    ],
                    "image" => $image,
                    "document" => $document,
                    "amount" => (int)$value,
                    "percent" => $calculatedPercent,
                    "total" => $total,
                ];
            }
        }

        return $designsArr;
    }

    /**
     * Подготовить Report данные для этого Printer { "1 - 1": 8, "1 - 2": 8, "1 - 3": 8 }
     *
     * @param $promotion_id
     * @param $filteredData
     * @return array
     */
    private function preparingReportArray($promotion_id, $filteredData): array {
        $promotionReport = PrintPromotionReport::where("promotion_id", $promotion_id)->first();
        // Собрать дизайны и посчитать amount   { "1 - 1": 8 }
        $aggregatedSurfaces = $this->collectDesignsCalculateAmount($promotionReport);

        // 2 Выбрать ids всех участвующих Surface в этом Promotion [1, 3]
        $uniqueSurfaceIds = $this->uniqueSurfaceIds($filteredData);

        // 3. Фильтровать aggregatedSurfaces на основе uniqueSurfaceIds
        $aggregatedSurfaces = array_filter($aggregatedSurfaces, function ($key) use ($uniqueSurfaceIds) {
            // Извлекаем первую цифру (surface ID) из ключа "1 - 1"
            $surfaceId = intval(explode(' - ', $key)[0]);
            return in_array($surfaceId, $uniqueSurfaceIds);
        }, ARRAY_FILTER_USE_KEY);

        return [$aggregatedSurfaces, $promotionReport->percent];
    }

    /**
     * Выбрать ids всех моих участвующих Surface в этом Promotion
     *
     * @param $filteredData
     * @return array
     */
    private function uniqueSurfaceIds($filteredData): array {
        $uniqueSurfaceIds = [];

        foreach ($filteredData as $item) {
            $surfaceId = $item->surface->id;

            if (!in_array($surfaceId, $uniqueSurfaceIds)) {
                $uniqueSurfaceIds[] = $surfaceId;
            }
        }

        return $uniqueSurfaceIds;
    }

    /**
     * Собрать дизайны и посчитать amount   { "1 - 1": 8 }
     *
     * @param $promotionReport
     * @return array
     */
    private function collectDesignsCalculateAmount($promotionReport): array {
        $surfaces = $promotionReport->surfaces;
        $aggregatedSurfaces = [];

        foreach ($surfaces as $surfaceItem) {
            foreach ($surfaceItem as $key => $value) {
                // Если ключ уже есть в массиве, добавляем значение
                if (isset($aggregatedSurfaces[$key])) {
                    $aggregatedSurfaces[$key] += $value;
                } else {
                    // Если ключа еще нет, инициализируем его значением
                    $aggregatedSurfaces[$key] = $value;
                }
            }
        }

        return $aggregatedSurfaces;
    }

}
