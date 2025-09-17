<?php

namespace App\Repositories;

use App\Jobs\SendUserAboutNewPromotionJob;
use App\Models\Design;
use App\Models\DistributorTracker;
use App\Models\PrintedPromotions;
use App\Models\PrintPromotionReport;
use App\Models\Promotion;
use App\Models\PromotionSurfaceDesign;
use App\Models\Surface;
use App\Models\SystemSetting;
use App\Models\Test;
use App\Models\User;
use App\Services\TrackerService;
use App\Services\XlFileService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Exception;
use Illuminate\Support\Facades\Schema;
use App\Jobs\SendAboutNewPromotionJob;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;

class PromotionRepository extends BaseRepository {

    /**
     * Выбирает promotions в пагинации
     * @param array $params
     * @return array
     */
    public function getPromotions(array $params, User $currentUser): array {

        $query = $this->baseQuery();

        // Если выбирает Дизайнер
        if ($currentUser->hasRole('designer')) {
            $query->where('status', ">", 0);
        }
        // Если выбирает Принтер
        elseif($currentUser->hasRole('printer')) {
            // 1 Только записи с подтверждением CM для Printers
            $query->whereNotNull("send_to_printer");

            // 2 Если хоть один из Surface в этом Promotion принадлежит этому Printer
            $query->whereIn('id', function ($subQuery) use ($currentUser) {
                $subQuery->select('promotion_id')->from('promotion_surfaces')
                    ->whereIn('surface_id', function ($surfaceQuery) use ($currentUser) {
                        $surfaceQuery->select('id')->from('surfaces')
                            ->where('printer_id', $currentUser->id);
                    });
            });
        }
        // Если выбирает Дистрибьютор
        elseif($currentUser->hasRole('distributor')) {
            // Выбрать уникальные записи отпечатанные Printer
            $printedIds = PrintedPromotions::pluck('promotion_id')->unique()->toArray();

            $query->whereIn('id', $printedIds);

            // 3. Добавляем выборку для связи с PrintedPromotions, если роль принтера
            $query->with('printedPromotions');
        }

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
        $promotions = $query->skip($params['start_index'])->take($params['count_show'])->get();

        return [
            'promotions' => $promotions,
            'total' => $total,
            'config_promotion_status' => config("site.promotion.status"),
        ];
    }

    /**
     * Создание promotion
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом создания.
     */
    public function createPromotion(array $validated, User $currentUser): array {
        try {
            // Подготавливаем данные для обновления
            $promotionData = $this->preparePromotionData($validated);

            // Создание promotion
            $promotion = Promotion::create($promotionData);

            if ($promotion) {

                return [
                    'success' => true,
                    'message' => 'Promotion saved successfully!',
                    'status_code' => 201,
                    'data' => ["promotion_id"=>$promotion->id],
                ];
            }
            else {
                return [
                    'success' => false,
                    'message' => 'Failed to create promotion.',
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
     * Обновить данные promotion.
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом обновления.
     */
    public function updatePromotion(array $validated, Promotion $promotion): array {
        try {
            // Подготавливаем данные для обновления
            $promotionData = $this->preparePromotionData($validated);

            // Обновляем поверхность в базе
            $updateSuccess = $promotion->update($promotionData);

            // Проверка на успешное обновление
            if ($updateSuccess) {
                // Обновляем объект promotion из базы данных
                $promotion->refresh();

                return [
                    'success' => true,
                    'message' => 'Promotion updated successfully!',
                    'status_code' => 200,
                    'data' => ["promotion_id"=>$promotion->id],
                ];
            }
            else {
                return [
                    'success' => false,
                    'message' => 'Failed to update promotion.',
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
     * @param Promotion $promotion
     * @return array
     */
    public function deletePromotion(User $currentUser, Promotion $promotion): array {
        // Проверяем роль текущего пользователя
        if (!$currentUser->hasRole('admin')) {
            return ['success' => false, 'message' => "You do not have permission to delete users.", 'status_code' => 403];
        }

        // Мягкое удаление
        $promotion->delete();

        return ['success' => true, 'message' => "Promotion successfully deleted.", 'status_code' => 200];
    }

    /**
     * Загрузка данных promotion-report-view страницы
     *
     * @param array $validated
     * @return array
     */
    public function promotionReportView(array $validated, User $currentUser): array {
        // 1 Promotion этой выборки
        $promotion = Promotion::find($validated['promotion_id']);
        $promotion->load('printPromotionReport');

        // 2 Взять процент этой Report promotion
        $trackService = new TrackerService($validated['promotion_id']);
        $percent = $trackService->getPercentPromotionReport();

        // 3 Создать массив XL данных юзеров
        $arrUsers = $this->doSummationDataDigits($trackService, $percent, $currentUser);
        // Добавить "printer_id": num, в обьекты с surface_id
        $arrUsers = $this->addPrinterIdInObjects($arrUsers);


        // 4 Посылки Printers этой Promotion
        $printedPromotions = PrintedPromotions::where("promotion_id",$validated['promotion_id'])
            ->with("printer")
            ->get();

        // 5 Выбрать всех юзеров XL Report
        $user = $arrUsers[0];
        $printerIds = [];
        // 5.1 Выбрать id Printers
        foreach ($user as $key => $value) {
            if (is_array($value) && isset($value['printer_id'])) {
                // Добавляем уникальные printer_id в массив
                $printerIds[] = $value['printer_id'];
            }
        }
        // Убираем дубликаты
        $printerIds = array_values(array_unique($printerIds));
        $useUsers = User::whereIn('id', $printerIds)->get(['id', 'name']);

        // 5.2 Выборка имен юзеров из Station обьекта XL
        $arrNames = [];
        foreach ($arrUsers as $user) {
            // Проверяем, если ключ Station существует и его значение не одно из исключений
            if (isset($user['Station']) && !in_array($user['Station'], ["", "Sum", "Percent", "Total number"])) {
                // Добавляем название в массив
                $arrNames[] = $user['Station'];
            }
        }
        $useUsers2 = User::whereIn('name', $arrNames)->get(['id', 'name']);
        // Объединяем две коллекции
        $allUsers = $useUsers->merge($useUsers2);

        // 6 Массив users (Компаний) с общим кол-во их поверхностей и уже отправленных дистрибьютером
        $arrUsersCountSurfaces = $trackService->getArrUsersWithCountSurfaces();

        // 7 Посылки Distributor
        $distributorParcels = DistributorTracker::where("promotion_id", $validated['promotion_id'])
            ->get();

        return [
            'success' => true,
            'message' => 'Promotion surfaces retrieved successfully.',
            'status_code' => 200,
            'data' => [
                "user_obj" => $arrUsers,
                "promotion" => $promotion,
                "percent" => $percent,
                "printed_promotions" => $printedPromotions,
                "use_users" => $allUsers,
                "arr_users_count_surfaces" => $arrUsersCountSurfaces,
                "distributor_parcels" => $distributorParcels,
            ]
        ];
    }

    /**
     * Сформировать данные статусов посылок Printers
     *
     * @param array $validated
     * @return array
     * @throws GuzzleException
     */
    public function getStatusPrinterParcels(array $validated, User $currentUser): array {
        // Все посылки этой Promotion
        $printedPromotionsQuery = PrintedPromotions::where("promotion_id", $validated['promotion_id']);

        // Если это Printer - ограниченная выборка из таблицы
        if ($currentUser->hasRole('printer')) {
            // Если роль пользователя — printer, добавляем фильтр по printer_id
            $printedPromotionsQuery->where("printer_id", $currentUser->id);
        }

        // Выполняем запрос
        $printedPromotions = $printedPromotionsQuery->get();

        $arrStatusParcels = [];

        // Выбрать статусы из сервиса https://tracking.bring.com
        foreach ($printedPromotions as $printed) {
            // Вызываем функцию для получения статуса
            $status = $this->getParcelStatus($printed->printer_tracker_number);

            // Добавляем статус в массив
            $arrStatusParcels[] = [
                "printer_id" => $printed->printer_id,
                "status" => $status,
                "surfaces" => $printed->sent_surfaces,
            ];
        }

        return [
            'success' => true,
            'message' => '',
            'status_code' => 200,
            'data' => $arrStatusParcels
        ];
    }

    public function getStatusDistributorParcels(array $validated): array {
        // Посылки Distributor
        $trackers = DistributorTracker::where("promotion_id", $validated['promotion_id'])
            ->get();

        $arrStatuses = [];

        // Выбрать статусы из сервиса https://tracking.bring.com
        foreach ($trackers as $tracker) {
            // Вызываем функцию для получения статуса
            $status = $this->getParcelStatus($tracker->tracker_number);

            // Добавляем статус в массив
            $arrStatuses[] = [
                "id" => $tracker->id,
                "status" => $status,
            ];
        }

        return [
            'success' => true,
            'message' => '',
            'status_code' => 200,
            'data' => $arrStatuses
        ];
    }

    /**
     * Оповестить Admin и Активация этой Promotion
     *
     * @param array $validated
     * @return array
     */
    public function notifyAdminAboutPromotion(array $validated): array {
        $promotion = Promotion::find($validated['promotion_id']);
        // Установка в статус 1
        $promotion->update([
            'show_in_user_promotions' => 1,
            'notify_admin' => now(),
            'status' => Promotion::STATUS_DESIGNER_WORKING
        ]);


        // Настройки системы
        $setting = SystemSetting::first();
        // Отправить admin письмо об этом promotion
        if($setting && !is_null($setting->admin_id)){
            $email = User::where('id', $setting->admin_id)
                ->pluck('email');

            // Поставить в задачи
            dispatch(new SendAboutNewPromotionJob($email, $promotion->id));
        }

        // Создает масив данных для каждого юзера участвующего в Promotion
        $usersDataArr = $this->generateDataUsersDesignsInSurfaces($validated['promotion_id']);

        foreach ($usersDataArr as $userData) {
            dispatch(new SendUserAboutNewPromotionJob($userData, $validated['promotion_id']));
        }

        return [
            'success' => true,
            'message' => 'A new Promotion has been created! Admin have been notified.',
            'status_code' => 200,
        ];
    }

    /**
     * Создает масив данных для каждого юзера участвующего в Promotion
     *
     * @param $promotion_id
     * @return array
     */
    private function generateDataUsersDesignsInSurfaces($promotion_id): array {
        $arrData = [
            'promotion_id' => $promotion_id,
            'promotion_name' => "",
            'display_address' => false,
            'display_categories' => false,
            'number_percent' => 10,
        ];
        $xlFileService = new XlFileService($arrData);
        $xlFileService->generateXLDate();
        $getXlDateArray = $xlFileService->getXlDateArray()->toArray();
        $stationNames = array_column($getXlDateArray, 'Station');

        $users = User::whereIn('name', $stationNames)
            ->select('name', 'email')
            ->get()
            ->keyBy('name'); // Упрощает поиск пользователей по имени

        $arr = [];

        foreach ($getXlDateArray as $stationData) {
            $userName = $stationData['Station'];

            if (!isset($users[$userName])) {
                continue; // Пропускаем станции, если нет пользователя
            }

            $user = $users[$userName];

            $properties = [];

            // Проходим по всем ключам в данных
            foreach ($stationData as $key => $value) {
                // Пропускаем нулевые значения
                if ($value > 0) {
                    // Разделяем ключ по знаку " - "
                    $parts = explode(' - ', $key);
                    if (count($parts) > 1) {
                        // Название свойства
                        $propertyName = $parts[0];
                        // Название элемента (после " - ")
                        $item = $parts[1];

                        // Если свойство еще не добавлено, создаем пустой массив для этого свойства
                        if (!isset($properties[$propertyName])) {
                            $properties[$propertyName] = [];
                        }

                        // Добавляем элемент в соответствующее свойство с его значением
                        $properties[$propertyName][$item] = $value;
                    }
                }
            }

            // Формируем итоговый массив
            $arr[] = array_merge(
                [
                    "user_name"  => $user->name,
                    "user_email" => $user->email,
                ],
                $properties
            );
        }

        // Фильтруем массив, удаляя объекты, у которых только user_name и user_email
        $arr = array_filter($arr, function ($item) {
            return count($item) > 2;
        });

        return array_values($arr);
    }

    /**
     * Получение статуса посылки
     *
     * @param string $trackerNumber
     * @return string
     * @throws GuzzleException
     */
    private function getParcelStatus(string $trackerNumber): string {
        $client = new Client();

        try {
            // Make the request
            $response = $client->request('GET', "https://tracking.bring.com/tracking/{$trackerNumber}");

            // Check if the status code is successful
            if ($response->getStatusCode() === 200) {
                // Get the HTML content
                $htmlContent = $response->getBody()->getContents();

                // Create a Crawler object to parse the HTML
                $crawler = new Crawler($htmlContent);

                // Search for the element with the required class
                $status = $crawler->filter('.hds-styled-html')->text();

                // If the status is found, return it
                if ($status) {
                    return $status;
                }
                else {
                    return 'Status not found';
                }
            }
            else {
                return "Response status: " . $response->getStatusCode();
            }
        }
        catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                if ($statusCode === 404) {
                    return "Address is invalid";
                }
                else {
                    return "Response status: " . $statusCode;
                }
            }
            else {
                return "Unable to connect: " . $e->getMessage();
            }
        }
    }

    /**
     * Добавить "printer_id": num, в обьекты с surface_id
     *
     * @param $arrUsers
     * @return mixed
     */
    private function addPrinterIdInObjects($arrUsers): mixed {
        // Шаг 1: Получаем уникальные surface_id
        $uniqueSurfaceIds = [];

        foreach ($arrUsers as $objUser) {
            foreach ($objUser as $property => $obj) {
                if (is_array($obj) && isset($obj['surface_id'])) {
                    $surfaceId = $obj['surface_id'];
                    if (is_numeric($surfaceId) && $surfaceId !== "") {
                        $uniqueSurfaceIds[$surfaceId] = true;
                    }
                }
            }
        }

        // Получаем ключи (уникальные surface_id)
        $uniqueSurfaceIds = array_keys($uniqueSurfaceIds);

        // Шаг 2: Получаем данные поверхностей с их printer_id
        $surfaces = Surface::whereIn("id", $uniqueSurfaceIds)
            ->get(['id', 'printer_id']);

        foreach ($arrUsers[0] as $property => $obj) {
            // Проверяем, есть ли surface_id и printer_id в объекте
            if (isset($obj['surface_id']) && is_numeric($obj['surface_id'])) {
                $surfaceId = $obj['surface_id'];

                // Ищем соответствующий surface по id в массиве $surfaces
                $surface = $surfaces->firstWhere('id', $surfaceId);
                if ($surface) {
                    $printerId = $surface->printer_id;

                    // Шаг 3: Перебираем все объекты в $arrUsers и добавляем printer_id
                    foreach ($arrUsers as &$user) {
                        if (isset($user[$property]) && is_array($user[$property])) {
                            // Добавляем printer_id в свойства с таким же именем
                            $user[$property]['printer_id'] = $printerId;
                        }
                    }
                }
            }
        }

        return $arrUsers;
    }

    /**
     * Создать массив XL данных юзеров
     *
     * @param $validated
     * @param $setting
     * @return array
     */
    private function doSummationDataDigits($trackService, $percent, $currentUser): array {
        // Данные юзеров XL файла
        $usersData = $trackService->getXlUsersData();

        // Получаем список surfaces и designs
        $surfaceMap = Surface::pluck('id', 'name')->toArray();
        $designMap = Design::pluck('id', 'name')->toArray();

        // Шаблон для пустого объекта
        $emptyObject = [];
        $result = [];

        // Обработка объекта XL
        foreach ($usersData as $user) {
            $newObject = [];

            foreach ($user as $key => $value) {
                // Если ключ не содержит " - ", просто переносим его в новый объект
                if (strpos($key, ' - ') === false) {
                    $newObject[$key] = $value;
                    $emptyObject[$key] = "";
                } else {
                    // Разбиваем ключ на части
                    [$surfaceName, $designName] = explode(' - ', $key);

                    // Проверяем существование surfaceName в $surfaceMap
                    if (isset($surfaceMap[$surfaceName])) {
                        $newObject[$key] = [
                            "surface_id" => (int)$surfaceMap[$surfaceName],
                            "design_id" => isset($designMap[$designName]) ? (int)$designMap[$designName] : null,
                            "amount" => (int)$value
                        ];
                        $emptyObject[$key] = [
                            "surface_id" => "",
                            "design_id" => "",
                            "amount" => ""
                        ];
                    }
                }
            }

            $result[] = $newObject;
        }

        // 2 Добавить пустой обьект
        $result[] = $emptyObject;

        // 3 Суммирование значений для последнего объекта
        $sumObject = $emptyObject;
        $sumObject['Station'] = "Sum";

        // Суммирование значений
        foreach ($result as $userData) {
            foreach ($userData as $key => $value) {
                // Проверяем, если ключ содержит " - ", то суммируем значение amount
                if (strpos($key, ' - ') !== false) {
                    // Убедимся, что amount — это число
                    $amount = !empty($value['amount']) ? (int)$value['amount'] : 0;

                    // Если поле 'amount' в $sumObject еще пустое, инициализируем его
                    if (empty($sumObject[$key]['amount'])) {
                        $sumObject[$key]['amount'] = 0;
                    }

                    // Добавляем к сумме
                    $sumObject[$key]['amount'] += $amount;
                }
            }
        }

        $result[] = $sumObject;

        // 4 Добавление нового объекта с процентами
        $newObjectWithPercent = $emptyObject;
        $newObjectWithPercent['Station'] = "Percent";

        // Проходим по объекту суммирования и вычисляем проценты
        foreach ($sumObject as $key => $value) {
            if (strpos($key, ' - ') !== false && isset($value['amount']) && $value['amount'] > 0) {
                // Вычисляем процент от суммы
                $percentAmount = ($value['amount'] / 100) * $percent;

                // Округляем результат в большую сторону до целого числа
                $percentAmount = ceil($percentAmount);

                // Записываем результат в новый объект
                $newObjectWithPercent[$key] = [
                    'amount' => $percentAmount
                ];
            }
        }

        $result[] = $newObjectWithPercent;

        // 5 Суммирование суммы и процента
        $newObjectWithSum = $emptyObject;
        $newObjectWithSum['Station'] = "Total number";

        // Проходим по объекту суммирования и суммируем значения из $sumObject и $newObjectWithPercent
        foreach ($sumObject as $key => $value) {
            // Если ключ содержит " - ", и есть значения для суммирования
            if (strpos($key, ' - ') !== false) {
                $sumAmount = isset($value['amount']) ? $value['amount'] : 0;
                $percentAmount = isset($newObjectWithPercent[$key]['amount']) ? $newObjectWithPercent[$key]['amount'] : 0;

                // Суммируем значения
                $totalAmount = $sumAmount + $percentAmount;

                // Записываем результат в новый объект
                $newObjectWithSum[$key] = [
                    'amount' => $totalAmount // Суммированное значение
                ];
            }
        }

        $result[] = $newObjectWithSum;

        // Добавить строки только если User имеет роли
        if ($currentUser->hasRoles(['admin', 'cm-admin'])) {
            // 6 Добавить пустой обьект
            $result[] = $emptyObject;

            // 7 добавляем строку Price per unit
            $priceUnit = $emptyObject;
            $priceUnit['Station'] = "Price per unit";

            $firstResult = $result[0] ?? [];
            $surfaceMap = Surface::pluck('price', 'id')->toArray();

            // Установить стоимость поверхности по прайсу ее Принтера
            foreach ($firstResult as $key => $value) {
                // Проверяем, содержит ли ключ " - " (пробел-тире-пробел)
                if (strpos($key, ' - ') !== false && is_array($value)) {
                    $surfaceId = $value['surface_id'] ?? null;

                    // Если surface_id найден, вставляем цену в $priceUnit
                    if ($surfaceId && isset($surfaceMap[$surfaceId])) {
                        $priceUnit[$key]['amount'] = $surfaceMap[$surfaceId];
                    }
                }
            }

            $result[] = $priceUnit;

            // 8 добавляем строку Total price
            $totalPrice = $emptyObject;
            $totalPrice['Station'] = "Total price";

            // Находим нужные обьекты
            $totalNumberObject = collect($result)->firstWhere('Station', 'Total number');
            $pricePerUnitObject = collect($result)->firstWhere('Station', 'Price per unit');

            // Установить общую стоимость количества этой поверхности с учетом цены Printer
            if ($totalNumberObject && $pricePerUnitObject) {
                foreach ($totalNumberObject as $key => $value) {
                    // Проверяем, содержит ли ключ " - " и является ли значением массивом с amount
                    if (strpos($key, ' - ') !== false && is_array($value) && isset($value['amount'])) {
                        // Общее количество этого surface
                        $totalItemInSurface = (float) $value['amount'];

                        // Берем цену из "Price per unit"
                        $unitPricePrinter = isset($pricePerUnitObject[$key]['amount']) ? (float) $pricePerUnitObject[$key]['amount'] : 0;

                        // Вычисляем общую сумму
                        $totalPrice[$key]['amount'] = $totalItemInSurface * $unitPricePrinter;
                    }
                }
            }

            $result[] = $totalPrice;
        }

        return $result;
    }

    /**
     * Подготавливает данные акции
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Данные поверхности.
     */
    private function preparePromotionData(array $validated): array {
        $promotionData = [
            'name' => $validated['name'] ?? 'Unknown',
            'period' => $validated['period'] ?? [],
            'show_in_user_promotions' => $validated['show_in_user_promotions'] ?? false,
            'description' => $validated['description'] ?? null,
            'who_created_id' => $validated['who_created_id'],
            'surfaces' => $validated['surfaces'] ?? []
        ];

        return $promotionData;
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
