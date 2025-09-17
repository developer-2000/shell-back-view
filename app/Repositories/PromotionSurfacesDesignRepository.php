<?php

namespace App\Repositories;

use App\Jobs\SendAboutDeletedDesignJob;
use App\Jobs\SendAboutNewDesignJob;
use App\Jobs\SendAboutNewPromotionJob;
use App\Jobs\SendPromotionEmailsToPrinters;
use App\Models\Design;
use App\Models\DesignChat;
use App\Models\PrintPromotionReport;
use App\Models\Promotion;
use App\Models\PromotionSurface;
use App\Models\PromotionSurfaceDesign;
use App\Models\Surface;
use App\Models\SystemSetting;
use App\Models\Test;
use App\Models\User;
use App\Services\FileStorageService;
use App\Services\XlFileService;
use Exception;
use Illuminate\Support\Facades\DB;


class PromotionSurfacesDesignRepository extends BaseRepository {

    /**
     * Все PromotionSurfaces с данными поверхности и всех дизайнов поверхности
     * @param array $validated
     * @return array
     */
    public function getAllPromotionSurfaces(array $validated): array {
        // 1. Выбираем все поверхности акции
        $promotionSurfaces = PromotionSurface::where('promotion_id', $validated['promotion_id'])
            ->get();

        // Если нет записей, возвращаем пустой массив
        if ($promotionSurfaces->isEmpty()) {
            return [
                'success' => true,
                'message' => 'no entries',
                'data' => [],
                'status_code' => 200
            ];
        }

        // Итоговый массив для ответов
        $result = $promotionSurfaces->map(function ($promotionSurface) use ($validated) {
            // 2. Выбираем поверхность по surface_id
            $surface = Surface::find($promotionSurface->surface_id);

            // Если поверхность не найдена, пропускаем эту запись
            if (!$surface) {
                return null;
            }

            // 3. Выбираем все дизайны для этой поверхности и акции
            $promotionSurfaceDesigns = PromotionSurfaceDesign::where('promotion_id', $validated['promotion_id'])
                ->where('surface_id', $promotionSurface->surface_id)
                ->get();

            // Формируем данные для этой записи
            return [
                'surface' => [
                    'id' => $surface->id,
                    'name' => $surface->name
                ],
                'designs' => $promotionSurfaceDesigns->map(function ($promotionSurface) {
                    $designCollect = Design::where("id", $promotionSurface->design_id)->first();

                    return [
                        'id' => $promotionSurface->id,
                        'name' => $designCollect ? $designCollect->name : "No name",
                        'category' => $promotionSurface->design_category_id,
                        'data' => $promotionSurface->data
                    ];
                })->toArray()
            ];
        })->filter()->values()->toArray(); // Фильтруем null и пересчитываем индексы

        return [
            'success' => true,
            'message' => 'Promotion surfaces retrieved successfully.',
            'status_code' => 200,
            'data' => $result
        ];
    }

    /**
     * Добавить дизайн к поверхности акции
     * @param array $validated
     * @return array
     */
    public function addDesignToSurfacePromotion(array $validated): array {
        DB::beginTransaction();

        try {
            // 1. Проверка, существует ли уже эта связка, включая мягко удаленные записи
            $existingEntry = PromotionSurfaceDesign::withTrashed()
                ->where('promotion_id', $validated['promotion_id'])
                ->where('surface_id', $validated['surface_id'])
                ->where('design_id', $validated['design_id'])
                ->first();

            if ($existingEntry) {
                // Восстановить мягко удаленную запись и чат
                if ($existingEntry->trashed()) {

                    $existingEntry->restore();
                    DesignChat::where('id', $existingEntry->chat_id)->restore();

                    // Проверка, была ли запись успешно восстановлена
                    if (!$existingEntry->exists) {
                        DB::rollBack();

                        return [
                            'success' => false,
                            'message' => 'Failed to restore PromotionSurfaceDesign record.',
                            'status_code' => 500
                        ];
                    }

                    DB::commit();

                    return [
                        'success' => true,
                        'message' => 'Design successfully restored to the surface promotion.',
                        'status_code' => 200
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Design already assigned to this surface in the promotion.',
                    'status_code' => 422
                ];
            }

            $design = Design::findOrFail($validated['design_id']);

            // 2. Создаем новый чат
            $chat = DesignChat::create([
                'messages' => []
            ]);

            // 3. Создаем новую запись
            $promotionSurfaceDesign = PromotionSurfaceDesign::create([
                'promotion_id' => $validated['promotion_id'],
                'surface_id' => $validated['surface_id'],
                'design_id' => $validated['design_id'],
                'chat_id' => $chat->id,
                'design_category_id' => $design->category_id,
            ]);

            // Проверяем, была ли запись успешно создана
            if (!$promotionSurfaceDesign->exists) {
                DB::rollBack();

                return [
                    'success' => false,
                    'message' => 'Failed to create PromotionSurfaceDesign record.',
                    'status_code' => 500
                ];
            }

            // 4. Если Дизайн создался в Promotion который имеет статус > 1 (designer working)
            // Изменить статус на designer working
            $promotion = Promotion::find($validated['promotion_id']);
            if($promotion->status > 1){
                // Установка в статус designer working
                $promotion->setDesignerWorkingStatus();
            }

            // Если всё прошло успешно
            DB::commit();

            return [
                'success' => true,
                'message' => 'Design successfully added to the surface promotion.',
                'status_code' => 200
            ];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Failed to add design to surface promotion: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Обновление данных brief дизайна
     * @param array $validated
     * @param PromotionSurfaceDesign $promotion_surface_designs
     * @return array
     */
    public function updateBriefSurfaceDesign(array $validated, PromotionSurfaceDesign $PSD): array {
        // Получаем текущее значение поля data
        $currentData = $PSD->data;

        // Обновляем данные, сохраняя поля files и products
        $updatedData = array_merge($currentData,
            [
                'title' => $validated['title'],
                'sub_title' => $validated['sub_title'],
                'text_italic' => $validated['text_italic'],
                'ean_more' => $validated['ean_more'],
                'promotional_offer' => $validated['promotional_offer'],
                'color' => $validated['color'],
                'supplier_discount' => $validated['supplier_discount'],
                'plu_scan' => $validated['plu_scan'],
                'status' => $validated['status'],
                'description' => $validated['description'],
                'additional_description' => $validated['additional_description'],
                'need_for_price' => $validated['need_for_price'],
                'not_for_printing' => $validated['not_for_printing'],
                'files' => $currentData['files'] ?? [],
                'products' => $currentData['products'] ?? [],
                ]
        );

        // Если статус Дизайна
        if ($updatedData['status'] === "Created") {
            // 1 Обновить статус Дизайна
            $updatedData['status'] = "Brief";

            // 2 Выслать Email Admin о новом Дизайне активной Promotion
            $this->SendAdminNotificationAboutNewDesign($PSD);
        }

        // Обновляем модель
        $PSD->data = $updatedData;
        $PSD->save();

        return ['success' => true, 'message' => "Promotion surface brief design updated successfully", 'status_code' => 200];
    }

    /**
     * Удалить дизайн из поверхности акции
     *
     * @param $currentUser
     * @param PromotionSurfaceDesign $promotion_surface_designs
     * @return array
     */
    public function deleteDesignFromSurfacePromotion($currentUser, PromotionSurfaceDesign $promotion_surface_designs): array {
        // Проверяем роль текущего пользователя
        if (!$currentUser->hasRoles(['admin', 'cm-admin'])) {
            return ['success' => false, 'message' => "You do not have permission to delete design.", 'status_code' => 403];
        }

        try {
            // 1 Отправка Email admin и cm-admin
            $this->notifyAboutDeletedDesign($currentUser, $promotion_surface_designs);

            // Найти связанный чат
            $chat = DesignChat::find($promotion_surface_designs->chat_id);

            // 2 Мягкое удаление дизайна
            $promotion_surface_designs->delete();

            // 3 Мягкое удаление чата, если он найден
            if ($chat) {
                $chat->delete();
            }

            return [
                'success' => true,
                'message' => 'Design removed successfully from the surface.',
                'status_code' => 200
            ];
        }
        catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete design from surface: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Загрузить Promotion, Surface, Design по id
     * @param array $validated
     * @return array
     */
    public function getPromotionSurfaceDesign(array $validated): array {

        $promotionData = Promotion::where('id', $validated['promotion_id'])
            ->first()
            ->toArray();

        $surfaceData = Surface::where('id', $validated['surface_id'])
            ->first(['id', 'name']);

        $surfaceDesignData = PromotionSurfaceDesign::where('id', $validated['surface_design_id'])
            ->with("designer")
            ->first();

        if(is_null($surfaceDesignData)){
            return [
                'success' => true,
                'status_code' => 200,
                'message' => 'Data not found',
                'data' => [],
            ];
        }

        // Получаем данные о дизайне
        $designCollect = Design::where("id", $surfaceDesignData->design_id)->first();
        // Добавляем свойство 'name' в данные о surfaceDesignData
        $surfaceDesignData->name = $designCollect ? $designCollect->name : "No name";

        // Добавляем surface и designs в массив surfaces
        $promotionData['surfaces'] = [
            [
                'surface' => [
                    'id' => $surfaceData->id,
                    'name' => $surfaceData->name,
                ],
                'design' => $surfaceDesignData
            ]
        ];

        return [
            'success' => true,
            'message' => 'Promotion surfaces retrieved successfully.',
            'status_code' => 200,
            'data' => $promotionData
        ];
    }

    /**
     * Обновить статус дизайна
     * @param int $chatId
     * @param int $rating
     * @return array
     */
    public function updateDesignStatus(int $chatId, int $statusDesignNum): array {
        $arrStatus = ["Brief", "Approved", "Completed"];

        // Находим запись по chat_id
        $surfaceDesign = PromotionSurfaceDesign::where("chat_id", $chatId)->first();

        if ($surfaceDesign) {
            $data = $surfaceDesign->data;

            // Если у этого Дизайна статус
            if ($data['status'] === "Created") {
                // 1 Выслать оповещение Admin если дизайн добавился к активной Promotion
                $this->SendAdminNotificationAboutNewDesign($surfaceDesign);
            }

            // 2 Обновить статус Дизайна
            $data['status'] = $arrStatus[$statusDesignNum];
            $surfaceDesign->data = $data;
            $surfaceDesign->save(); // Сохраняем изменения в базе данных

            return [
                'success' => true,
                'message' => "Design status updated successfully.",
                'status_code' => 200,
            ];
        }

        return [
            'success' => false,
            'message' => 'Surface design not found.',
            'status_code' => 404,
        ];
    }

    /**
     * Добавление продукта в brief дизайн
     * @param array $validated
     * @return array
     */
    public function addProductBriefDesign(array $validated): array {
        // Находим дизайн по идентификатору
        $design = PromotionSurfaceDesign::find($validated['promotion_surface_design_id']);

        if (!$design) {
            return [
                'success' => false,
                'message' => "Design with id {$validated['promotion_surface_design_id']} not found.",
                'status_code' => 404,
            ];
        }

        // Получаем текущие данные дизайна
        $data = $design->data;

        // Добавляем продукт в массив products
        $data['products'][] = [
            'product_id' => $validated['product_id']
        ];

        // Обновляем поле data
        $design->data = $data;

        // Сохраняем изменения
        $design->save();

        return [
            'success' => true,
            'message' => 'Product added to design successfully.',
            'status_code' => 200,
        ];
    }

    /**
     * Удаление продукта из brief дизайна
     * @param array $validated
     * @return array
     */
    public function deleteProductBriefDesign(array $validated): array {
        // Находим дизайн по идентификатору
        $design = PromotionSurfaceDesign::find($validated['promotion_surface_design_id']);

        // Получаем текущие данные дизайна
        $data = $design->data;

        // Удаляем продукт с заданным product_id из массива products
        $data['products'] = array_values(array_filter($data['products'], function($product) use ($validated) {
            return $product['product_id'] !== $validated['product_id'];
        }));

        // Обновляем поле data
        $design->data = $data;

        // Сохраняем изменения
        $design->save();

        return [
            'success' => true,
            'message' => 'Product removed from design successfully.',
            'status_code' => 200,
        ];
    }

    /**
     * Сохранить файлы для Design Brief
     * @param array $validated
     * @return array
     */
    public function setFilesBriefDesign(array $validated): array {
        try {
            $designCollection = PromotionSurfaceDesign::find($validated['promotion_surface_design_id']);
            // Получение разрешенных расширений из конфигурации
            $extensions = config('site.files');
            // Создаем массив для хранения объектов файлов
            $filesData = [];
            $newFiles = [];

            // Собираем каждый файл в отдельный объект данных
            foreach ($validated['files'] as $index => $file) {
                $filesData[] = [
                    'url' => $file,
                    'name' => $validated['names'][$index] ?? null,
                    'size' => $validated['sizes'][$index] ?? null,
                    'date' => $validated['dates'][$index] ?? null,
                ];
            }

            // Функция для определения следующего id
            $nextId = $this->getNextId($designCollection->data['files'] ?? []);

            // Проверка расширений и вызов соответствующих методов
            foreach ($filesData as $file) {
                if ($file['name']) {
                    // Получаем расширение файла
                    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $switch_file = null;

                    // Если это изображение
                    if (in_array($extension, $extensions['extensions_image'])) {
                        $switch_file = "images";
                    }
                    // Если это документ
                    elseif (in_array($extension, $extensions['extensions_document'])) {
                        $switch_file = "documents";
                    }
                    else{
                        return [
                            'success' => false,
                            'message' => "Unsupported file extension.",
                            'status_code' => 400
                        ];
                    }

                    // Только картинке меняем размеры
                    $size = $switch_file == "documents" ? null : $file['size'];

                    $responseUrl = $this->processUploadForDesignChat(
                        $file['name'],
                        $file['url'],
                        'uploads/design-brief/design-brief-id-' . $validated['promotion_surface_design_id'] . '/' . $switch_file,
                        $size
                    );

                    if (!$responseUrl['success']) {
                        return $responseUrl;
                    }

                    // Создаем объект для добавления в массив files
                    $newFiles[] = [
                        "id" => ++$nextId,
                        "extension" => $extension,
                        "name" => $file['name'],
                        "url" => $responseUrl['new_url'],
                        "date" => $file['date'],
                    ];
                }
            }

            // Сохраняем изменения в базе данных
            if ($designCollection) {
                $data = $designCollection->data ?? [];
                // Обьеденить имеющиеся файлы с текущими
                $data['files'] = array_merge($data['files'] ?? [], $newFiles);
                $designCollection->data = $data;
                $designCollection->save();
            }

            return [
                'success' => true,
                'message' => 'Files updated successfully!',
                'status_code' => 200,
            ];
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
     * Удалить файл из brief дизайна
     * @param array $validated
     * @return array
     */
    public function deleteFileBriefDesign(array $validated): array {
        try {
            // Находим дизайн
            $designCollection = PromotionSurfaceDesign::find($validated['promotion_surface_design_id']);

            if (!$designCollection || empty($designCollection->data['files'])) {
                return [
                    'success' => false,
                    'message' => 'Design not found or no files to delete.',
                    'status_code' => 404,
                ];
            }

            // Ищем файл по ID в массиве files
            $files = $designCollection->data['files'];
            $fileIndex = array_search($validated['file_id'], array_column($files, 'id'));

            if ($fileIndex === false) {
                return [
                    'success' => false,
                    'message' => 'File not found.',
                    'status_code' => 404,
                ];
            }

            // 1 Удаление старой картинки в хранилище
            $fileStorageService = new FileStorageService();
            $fileObj = $files[$fileIndex];

            if (isset($fileObj['url'])) {
                $responseDelete = $fileStorageService->deleteFileByUrl($fileObj['url']);

                if (!$responseDelete['success']) {
                    return [
                        'success' => false,
                        'message' => $responseDelete['message'],
                        'status_code' => $responseDelete['status_code'],
                    ];
                }
            }

            // 2 Удаляем файл из массива
            unset($files[$fileIndex]);
            $updatedFiles = array_values($files); // Пересобираем массив

            // Обновляем данные модели
            $data = $designCollection->data ?? [];
            $data['files'] = $updatedFiles;
            $designCollection->data = $data;
            $designCollection->save();

            return [
                'success' => true,
                'message' => 'File deleted successfully!',
                'status_code' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Оповестить юзеров (Printer) участвующих в этом Promotion о статусе Completed дизайнов их Surfaces
     *
     * @param array $validated
     * @return array
     */
    public function sendingNotificationPrinters(array $validated): array {
        try {
            // Для формирования столбцов и цифр
            $arrData = [
                'promotion_id' => $validated['promotion_id'],
                'promotion_name' => "",
                'display_address' => true,
                'display_categories' => false,
                'number_percent' => $validated['percent_report'],
            ];

            $xlFileService = new XlFileService($arrData);
            $xlFileService->generateXLDate();
            $objectDb = $xlFileService->getObjectDb();

            // 1 Зафиксировать данные для Printer
            PrintPromotionReport::updateOrCreate(
                ['promotion_id' => $validated['promotion_id']],
                [
                    'percent' => $validated['percent_report'],
                    'description_cm' => $validated['description_cm'],
                    'surfaces' => $objectDb,
                ]
            );

            // 2 Получаем все записи, где promotion_id совпадает с переданным значением
            $promotionSurfaceDesigns = PromotionSurfaceDesign::where('promotion_id', $validated['promotion_id'])
                ->with("surface", 'surface.printer')
                ->get();

            $surfaceNamesNotPrinter = [];
            $uniquePrinters = [];

            // 3 Вернуть сообщение на фронт о том что не у всех Surface есть Printers
            // Собрать имена Surfaces у которых нет юзера (Printer)
            foreach ($promotionSurfaceDesigns as $item) {
                // У этой Surface нет назначенного Printer
                if (is_null($item['surface']['printer'])) {
                    // Имя поверхности еще не добавлено в массив
                    if (!in_array($item['surface']['name'], $surfaceNamesNotPrinter)) {
                        $surfaceNamesNotPrinter[] = $item['surface']['name'];
                    }
                }
            }
            // Вывод названий этих Surfaces
            if(count($surfaceNamesNotPrinter)){
                return [
                    'success' => true,
                    'message' => '',
                    'data' => $surfaceNamesNotPrinter,
                    'status_code' => 200,
                ];
            }

            // 4. Собрать уникальные email и name из принтеров
            foreach ($promotionSurfaceDesigns as $item) {
                $printer = $item['surface']['printer'];
                if ($printer && !in_array($printer['email'], array_column($uniquePrinters, 'email'))) {
                    $uniquePrinters[] = [
                        'name' => $printer['name'],
                        'email' => $printer['email'],
                    ];
                }
            }

            // 5. Устанавливаем дату завершения работ Дизайнера и принятия Принтером
            $promotion = Promotion::where('id', $validated['promotion_id'])->first();
            $promotion->send_to_printer = now();
            $promotion->save();

            // 6. Отправить письма всем Printer чьи Surface получили Completed
            $this->sendMailToPrinters($uniquePrinters, $validated['promotion_id']);

            return [
                'success' => true,
                'message' => 'All Printers were successfully notified.',
                'data' => [],
                'status_code' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Отправить всем юзерам (Printer) уведомление о готовых дизайнах
     *
     * @param array $uniquePrinters
     * @param int $promotionId
     * @return void
     */
    private function sendMailToPrinters(array $uniquePrinters, int $promotionId): void {
        dispatch(new SendPromotionEmailsToPrinters($uniquePrinters, $promotionId));
    }

    // Функция для определения следующего id массива файлов
    private function getNextId(array $files): int {
        if (empty($files)) {
            return 0;
        }
        // Находим максимальный id
        return max(array_column($files, 'id'));
    }

    /**
     * Выслать Email Admin если дизайн добавился к активной Promotion
     *
     * @param $PSD
     * @return void
     */
    private function SendAdminNotificationAboutNewDesign($PSD): void {
        $promotion = Promotion::find($PSD->promotion_id);

        // Если статус Promotion > 0
        if ($promotion && $promotion->status > 0) {
            // Настройки системы
            $setting = SystemSetting::first();

            if ($setting && !is_null($setting->admin_id)) {
                $email = User::where('id', $setting->admin_id)->pluck('email')->toArray();
                $promotion_link = url("https://new.st1shop.no/promotion-settings?prom_id={$PSD->promotion_id}&action=surfaces");
                $design_link = url("https://new.st1shop.no/promotion-design?prom_id={$PSD->promotion_id}&sur_id={$PSD->surface_id}&prom_sur_des_id={$PSD->id}");
                // Поставить в задачи
                dispatch(new SendAboutNewDesignJob($email, $promotion_link, $design_link));
            }
        }
    }

    private function notifyAboutDeletedDesign($currentUser, PromotionSurfaceDesign $promotion_surface_designs) {
        $status = $promotion_surface_designs->data['status'];

        // Если ты admin ИЛИ cm-admin и статус дизайна ниже ("Approved, Completed")
        if ( $currentUser->hasRole('admin') ||
            ($currentUser->hasRole('cm-admin') && ($status !== "Approved" && $status !== "Completed"))
        ) {

            // Если Designer подключен к дизайну
            if ($promotion_surface_designs->designer_id) {
                $promotion_surface_designs->load(['designer', 'promotion', 'surface', 'design']);

                // 1 Отправить Designer Email
                if ($promotion_surface_designs->designer->email) {
                    $this->sendNotification(
                        $promotion_surface_designs->designer->email,
                        $promotion_surface_designs->designer->name ?? 'Unknown Name',
                        $promotion_surface_designs->promotion->name ?? 'Unknown Promotion',
                        $promotion_surface_designs->surface->name ?? 'Unknown Surface',
                        $promotion_surface_designs->design->name ?? 'Unknown Design',
                        url("https://new.st1shop.no/promotion-settings?prom_id={$promotion_surface_designs->promotion_id}&action=surfaces")
                    );
                }

                // 2 Если роль 'cm-admin', также уведомляем admin
                if ($currentUser->hasRole('cm-admin')) {
                    $this->notifyAdmin($promotion_surface_designs);
                }
            }
        }


    }

    private function sendNotification($email, $userName, $promotionName, $surfaceName, $designName, $promotionLink) {
        dispatch(new SendAboutDeletedDesignJob($email, $userName, $promotionName, $surfaceName, $designName, $promotionLink));
    }

    private function notifyAdmin(PromotionSurfaceDesign $promotion_surface_designs) {
        $settings = SystemSetting::first();

        // Проверка на наличие настроек и admin_id
        if ($settings && $settings->admin_id) {
            $admin = User::find($settings->admin_id);

            // Если администратор найден и у него есть email, отправляем уведомление
            if ($admin && $admin->email) {
                $this->sendNotification(
                    $admin->email,
                    $admin->name ?? 'Unknown Name',
                    $promotion_surface_designs->promotion->name ?? 'Unknown Promotion',
                    $promotion_surface_designs->surface->name ?? 'Unknown Surface',
                    $promotion_surface_designs->design->name ?? 'Unknown Design',
                    url("https://new.st1shop.no/promotion-settings?prom_id={$promotion_surface_designs->promotion_id}&action=surfaces")
                );
            }
        }
    }

}
