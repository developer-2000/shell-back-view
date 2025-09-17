<?php
namespace App\Services;

use App\Models\Test;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileStorageService {

    protected string $temporaryServiceUrl;

    public function __construct() {
        $this->temporaryServiceUrl = "https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/";
    }

    /**
     * Сохраняет файл (image, document) в хранилище
     *
     * @param $base64File
     * @param $fileName
     * @param $folder
     * @return string
     * @throws \Exception
     */
    public function saveBase64FileToStorage( $base64File, $fileName, $folder = 'uploads/files', $size ): string {

        // Проверяем и извлекаем данные
        if (preg_match('/^data:(.*?);base64,(.*)$/', $base64File, $matches)) {
            $mimeType = $matches[1]; // Получаем MIME-тип файла
            $base64Data = $matches[2]; // Закодированные данные

            // Декодируем данные
            $decodedData = base64_decode($base64Data);
            if ($decodedData === false) {
                throw new \Exception('Invalid base64 data');
            }
        }
        else {
            throw new \Exception('Invalid base64 format');
        }

        // Создаем уникальную подпапку
        $uniqueFolder = $folder . '/' . uniqid();
        $filePath = $uniqueFolder . '/' . $fileName;

        // Сохраняем файл в хранилище
        try {

            // Images
            if($size){
                // Открываем изображение для изменения размера
                $manager = new ImageManager(new Driver()); // Инициализируем ImageManager с драйвером Gd
                // Используем метод read() для загрузки изображения
                $image = $manager->read($decodedData);

                // Если размер не "original" и в начале w (w_200)
                if ($size !== 'original' && preg_match('/^w_(\d+)$/', $size, $matches)) {
                    $width = (int)$matches[1];
                    // Получаем исходные размеры изображения
                    $originalWidth = $image->width();

                    if ($width < $originalWidth) {
                        // Пропорционально меняет размеры по 1 параметру
                        $image->scale(width: $width);
                    }
                }

                // Сохраняем измененное изображение
                Storage::disk('r2')->put($filePath, $image->encode());
            }
            // Document
            else{
                Storage::disk('r2')->put($filePath, $decodedData);
            }

            return $this->temporaryServiceUrl . $filePath;
            // return Storage::disk('r2')->url($filePath);
        }
        catch (\Exception $e) {
            throw new \Exception('File upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Удаление файла в хранилище по url
     */
    public function deleteFileByUrl($imgUrl): array {
        // Убедимся, что URL начинается с временной службы
        if (strpos($imgUrl, $this->temporaryServiceUrl) === 0) {
            // Извлекаем путь к файлу из URL
            $filePath = str_replace($this->temporaryServiceUrl, '', $imgUrl);

            // Проверяем, существует ли файл в хранилище
            if (Storage::disk('r2')->exists($filePath)) {
                // Удаляем файл из хранилища
                if (Storage::disk('r2')->delete($filePath)) {
                    return [
                        'success' => true,
                        'message' => 'File successfully deleted.',
                        'status_code' => 200,
                    ];
                }
                else {
                    return [
                        'success' => false,
                        'message' => 'Error deleting file by url',
                        'status_code' => 500,
                    ];
                }
            }
            else {
                Log::warning("File for deletion by URL not found: {$imgUrl}. File path: {$filePath}.");
                return [
                    'success' => true,
                    'message' => 'File for deletion by url not found',
                    'status_code' => 200,
                ];
            }
        }

        Log::error("Invalid URL format: {$imgUrl}.");
        return [
            'success' => false,
            'message' => 'Invalid URL format.',
            'status_code' => 400,
        ];
    }

}


