<?php

namespace App\Services;

use App\Models\Category;
use App\Models\PrintPromotionReport;
use App\Models\PromotionSurfaceDesign;
use App\Models\Test;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class XlFileService implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize {

    protected $users;
    protected array $validatedData;
    protected $xlDateArray = [];
    protected array $xlHeaderArray = [];
    protected array $xlStyleArray = [];
    protected array $objectPromotionSurfaces = [];
    protected array $objectDb = [];


    public function __construct(array $validatedData) {
        $this->validatedData = $validatedData;

        // Получаем всех пользователей с ролью 'user'
        $this->users = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })
            ->with('userData', 'roles')
            ->get();

        if ($this->users->isNotEmpty()) {
            $this->users = $this->users->map(function ($user) {
                // Возвращаем массив с объединёнными данными пользователя
                return $user->merged_user_data;
            });
        }
    }

    /**
     * Основная функция создания XL файла
     * @param array $validatedData
     * @return array
     */
    public function makeFile(): array {
        $promotion = $this->validatedData["promotion_name"];
        // Генерируем имя файла с сегодняшней датой
        $date = date('d_m_Y_H_i_s');
        $fileName = "{$date}_" . strtolower(str_replace(' ', '_', $promotion)) . ".xlsx";

        // Указываем путь для сохранения файла в папку public/xl_files
        $filePath = public_path("xl_files/{$fileName}");

        $directoryPath = public_path('xl_files');
        // Создать папку xl_files если ее нет
        if (!File::exists($directoryPath)) {
            File::makeDirectory(public_path('xl_files'), 0755, true);
        }
        // Удаляем все файлы в папке xl_files
        else {
            File::cleanDirectory($directoryPath);
        }

        // Сохраняем Excel файл
        Excel::store($this, $fileName, 'local');
        // Перемещаем файл в папку public
        File::move(storage_path("app/$fileName"), $filePath);

        return [
            'success' => true,
            'status_code' => 200,
            'message' => 'File created successfully',
            'data' => ['file' => url("xl_files/{$fileName}") ]
        ];
    }

    /**
     * Генерирует столбцы с их данными
     *
     * @return void
     */
    public function generateXLDate(): void {
        // 1 Создать объект акции с его поверхностями
        $this->createObjectPromotionWithSurfaces();

        // 2 Создать столбики xl файла
        $this->xlDateArray = $this->users->map(function ($user) {
            // 1 Столбик Названия компании (юзера)
            $userData = [ 'Station' => $user['name'] ?? 'N/A' ];
            $this->makeArrayHeaderInRow('Station');

            // 2 Столбик Address Компании (юзера), если display_address включен
            if (!empty($this->validatedData["display_address"])) {
                $userData['Address'] = $user['post_address'] ?? 'N/A';
                $this->makeArrayHeaderInRow('Address');
            }

            // 3 Столбики имеющихся категорий Компании (юзера)
            if (!empty($this->validatedData["display_categories"])) {
                $categoryNames = Category::select('id', 'name')->get()->toArray();
                foreach ($categoryNames as $category) {
                    $userData[$category['name']] = collect($user['company_categories'])->contains('id', $category['id']) ? 'Yes' : '';
                    $this->makeArrayHeaderInRow($category['name']);
                }
            }

            // 4 Столбики количества в дизайне поверхности Компании (юзера)
            $userData = $this->getCompanyDesignCounts($user, $userData);

            return $userData;
        });

    }

    /**
     * Вернуть сформированный обьект юзеров
     * @return mixed
     */
    public function getXlDateArray(): mixed {
        return $this->xlDateArray;
    }

    public function getObjectDb(): array {
        return $this->objectDb;
    }

    protected function getCompanyDesignCounts($user, $userData): array {
        $data = ["user_name" => $user["name"]];

        // Перебрать обьект поверхностей
        foreach ($this->objectPromotionSurfaces as $key => $surfaceArr) {
            // divided_bool этого surface = true / false
            // null если такой surface у user нет
            $dividedBool = $this->getDividedBool($user, $key);
            // Количество дизайнов в поверхности
            $countDesign = count($surfaceArr);

            // Перебор обьектов дизайнов
            foreach ($surfaceArr as $index =>  $surface) {
                // Передача значения heading как заголовка
                $this->makeArrayHeaderInRow($surface['heading']);

                // Amount surface в user настройках (Компании)
                // Если null - у user нет такой настройки
                $amountSurface = $this->getAmountSurfaceAtCompany($user, $key);
                $amount = 0;
                $color = "";

                if (is_numeric($amountSurface) && is_numeric($countDesign)) {
                    // 1 Если включен divided_bool у этой поверхности
                    if($dividedBool){
                        // 1.1
                        if ($countDesign >= $amountSurface) {
                            $amount = 1;
                            $color = $surface['completed'] ? "green" : "red";
                        }
                        // 1.2
                        else if ($countDesign < $amountSurface) {
                            $round = 1;
                            $color = $surface['completed'] ? "green" : "red";
                            // Перекрыть amount surface user (Компании)
                            while(1) {
                                if(($countDesign * $round) >= $amountSurface){
                                    $amount = $round;
                                    break;
                                }
                                $round++;
                            }
                        }
                    }
                    // 2 Если Не включен divided_bool у этой поверхности
                    else{
                        $amount = $amountSurface;
                        $color = $surface['completed'] ? "green" : "red";
                    }
                }

                $userData[$surface['heading']] = $amount;
                $data[] = [$surface['heading'] => $color];
                $this->objectDb[] = [$surface['ids'] => $amount];
            }
        }

        $this->xlStyleArray[] = $data;
        return $userData;
    }

    /**
     * Возвращает divided_bool у surface = true / false
     * null если такой настройки у user нет
     *
     * @param $user
     * @param $key
     * @return mixed|null
     */
    protected function getDividedBool($user, $key): mixed {
        // Выбрать у user (Компания) настройку поверхности по id
        $companyPlannerSurface = collect($user['company_planner'])->firstWhere('surface.id', $key);
        // Значение у поверхности divided_bool = true/false или null если такой настройки у user нет
        return $companyPlannerSurface ? $companyPlannerSurface['surface']['divided_bool'] : null;
    }

    /**
     * Вернуть amount указанной surface
     *
     * @param $user
     * @param $key
     * @return mixed
     */
    protected function getAmountSurfaceAtCompany($user, $key): mixed {
        // Ищем в массиве company_planner нужную поверхность по $key
        $companyPlannerSurface = collect($user['company_planner'])->firstWhere('surface.id', $key);
        // Получаем значение amount, если такая поверхность найдена
        return $companyPlannerSurface ? (int)$companyPlannerSurface['amount'] : null;
    }

    // Создать объект акции с его поверхностями
    protected function createObjectPromotionWithSurfaces(): void {
        $promotionObjs = PromotionSurfaceDesign::where("promotion_id", $this->validatedData["promotion_id"])
            ->whereNull("deleted_at")
            ->with("surface", "design")
            ->get();

        $grouped = [];

        foreach ($promotionObjs as $index => $item) {
            $key = $item->surface_id;

            // Создать новую группу
            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }

            if (isset($item->surface->name) && isset($item->design->name)) {
                // Добавить объект в группу
                $grouped[$key][] = [
                    'ids' => $item->surface->id . " - " . $item->design->id,
                    'heading' => $item->surface->name . " - " . $item->design->name,
                    'completed' => $item->data['status'] === "Completed"
                ];
            }
        }

        $this->objectPromotionSurfaces = $grouped;
    }

    /**
     * Создает массив заголовков столбцов
     *
     * @param $header
     * @return void
     */
    protected function makeArrayHeaderInRow($header): void {
        if (!in_array($header, $this->xlHeaderArray)) {
            $this->xlHeaderArray[] = $header;
        }
    }

    /**
     * Возвращает коллекцию контента файла
     *
     * @return Collection
     */
    public function collection(): Collection {
        return collect($this->xlDateArray);
    }

    /**
     * Возвращает массив заголовков файла
     *
     * @return array
     */
    public function headings(): array {
        return $this->xlHeaderArray;
    }

    /**
     * Устанавливает название страницы
     * @return string
     */
    public function title(): string {
        return 'Brief';
    }

    /**
     * Стилизация файла
     *
     * @param Worksheet $sheet
     * @return array
     * @throws Exception
     */
    public function styles(Worksheet $sheet): array {
        $highestRow = $sheet->getHighestRow();
        // Первая строка - жирный шрифт, выравнивание по центру, черный цвет
        $highestColumn = $sheet->getHighestColumn(); // Получаем последний столбец
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([ // Применяем стиль ко всей первой строке
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // Черный цвет текста
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Весь текст на странице - черного цвета, не жирный
        $sheet->getStyle('A2:A' . $sheet->getHighestRow())
            ->applyFromArray([
                'font' => [
                    'bold' => false,
                    'color' => ['rgb' => '000000'], // Черный цвет текста
                ],
            ]);

        // Вывод строк подсчета столбцов
        $this->outputColumnCountRows($highestRow, $highestColumn, $sheet);

        // Подсветка текста в ячейках
        foreach ($this->xlStyleArray as $styleItem) {
            $userName = $styleItem['user_name']; // Имя пользователя
            $userRow = null;

            // Найти строку с user_name
            for ($row = 1; $row <= $highestRow; $row++) {
                if ($sheet->getCell("A{$row}")->getValue() === $userName) {
                    $userRow = $row;
                    break;
                }
            }

            if ($userRow) {
                foreach ($styleItem as $key => $value) {
                    if ($key !== 'user_name') {
                        foreach ($value as $columnName => $color) {
                            // Найти столбец с названием, например, "Big banner - Cherry compote"
                            $columnIndex = null;
                            for ($col = 'A'; $col <= $highestColumn; $col++) {
                                if ($sheet->getCell("{$col}1")->getValue() === $columnName) {
                                    $columnIndex = $col;
                                    break;
                                }
                            }

                            if ($columnIndex && $color) {
                                // Применить цвет текста
                                $sheet->getStyle("{$columnIndex}{$userRow}")->applyFromArray([
                                    'font' => [
                                        'color' => ['rgb' => $this->mapColorToRgb($color)],
                                    ],
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return [];
    }

    /**
     * Преобразует имя цвета в RGB-значение
     *
     * @param string $color
     * @return string
     */
    private function mapColorToRgb(string $color): string {
        return match (strtolower($color)) {
            'green' => '16a34a',
            'red' => 'dc2626',
            default => '000000',
        };
    }

    /**
     * Вывод строк подсчета столбцов
     *
     * @param $highestRow
     * @param $highestColumn
     * @param $sheet
     * @return void
     */
    protected function outputColumnCountRows($highestRow, $highestColumn, $sheet): void {
        // 1 Добавляем надпись "Sum"
        $sumRow = $highestRow + 2;
        $sheet->setCellValue("A{$sumRow}", 'Sum');
        $sheet->getStyle("A{$sumRow}")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Заполняем ячейки формулами для суммирования
        $columnIndex = 1;
        foreach ($this->xlHeaderArray as $header) {
            if (strpos($header, '-') !== false) {
                $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                $formula = "=SUM({$columnLetter}2:{$columnLetter}{$highestRow})";
                $sheet->setCellValue("{$columnLetter}{$sumRow}", $formula);
            }
            $columnIndex++;
        }

        // 2 Добавляем надпись "Percent"
        $percentRow = $sumRow + 1;
        $sheet->setCellValue("A{$percentRow}", 'Percent');
        $sheet->getStyle("A{$percentRow}")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Заполняем ячейки формулами для расчета Процентов
        $columnIndex = 1;
        foreach ($this->xlHeaderArray as $header) {
            if (strpos($header, '-') !== false) {
                $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                $formula = "=ROUNDUP({$columnLetter}{$sumRow}*" . ($this->validatedData["number_percent"] / 100) . ", 0)";
                $sheet->setCellValue("{$columnLetter}{$percentRow}", $formula);
            }
            $columnIndex++;
        }

        // 3. Добавляем надпись "Total number"
        $totalRow = $percentRow + 1;
        $sheet->setCellValue("A{$totalRow}", 'Total number');
        $sheet->getStyle("A{$totalRow}:{$highestColumn}{$totalRow}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'dcfce7'],
            ],
        ]);
        $sheet->getStyle("A{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Заполняем ячейки формулами для суммирования суммы и процента
        $columnIndex = 1;
        foreach ($this->xlHeaderArray as $header) {
            if (strpos($header, '-') !== false) {
                $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                $formula = "={$columnLetter}{$sumRow} + {$columnLetter}{$percentRow}";
                $sheet->setCellValue("{$columnLetter}{$totalRow}", $formula);
            }
            $columnIndex++;
        }
    }

}
