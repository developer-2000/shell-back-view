<?php

namespace App\Repositories;

use App\Services\FileStorageService;


class BaseRepository {

    /**
     * Сохраняет в хранилище файл image (для обычных страниц)
     * @param array $validated
     * @param string $modelClass
     * @return array
     */
    protected function processImageUpload(array &$validated, string $modelClass): array {
        $fileStorageService = new FileStorageService();

        if (!empty($validated['img_url']) && isset($validated['img_name'])) {

            // 1 Удаление старой картинки в хранилище
            if (isset($validated['id'])) {
                $modelInstance = $modelClass::where("id", $validated['id'])->first();

                if ($modelInstance && !is_null($modelInstance->url_images)) {
                    // Перебираем массив url_images с url image
                    foreach ($modelInstance->url_images as $size => $url) {
                        // Удаляем старое изображение
                        $responseDeleteImage = $fileStorageService->deleteFileByUrl($url);
                        if (!$responseDeleteImage['success']) {
                            return [
                                'success' => false,
                                'message' => $responseDeleteImage['message'],
                                'status_code' => 500
                            ];
                        }
                    }
                }
            }

            // 2 Сохранить переданную картинку в хранилище
            try {
                // Перебираем make_size для обработки размеров
                $imageUrls = [];

                foreach ($validated['make_size'] as $size) {

                    // Уникальное имя файла для каждого размера
                    $sizeSuffix = '';
                    if ($size === "original") {
                        $sizeSuffix = 'original';
                    }
                    elseif (preg_match('/^w_(\d+)$/', $size, $matches)) {
                        $width = (int)$matches[1];
                        $sizeSuffix = 'w_' . $width;
                    }

                    // Сохранить изображение с учетом размера
                    $imagePath = $fileStorageService->saveBase64FileToStorage(
                        $validated['img_url'],
                        $validated['img_name'],
                        'uploads/' . strtolower(class_basename($modelClass)) . '/images/' . $sizeSuffix,
                        $size
                    );

                    $imageUrls[$size] = $imagePath;
                }

                return ['success' => true, 'image_urls' => $imageUrls];
            }
            catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'status_code' => 500
                ];
            }
        }

        return ['success' => true, 'validated' => $validated];
    }

    protected function processUploadForDesignChat( string $file_name, string $file_url, string $unique_url, $size ): array {
        $fileStorageService = new FileStorageService();

        // Был выбран файл
        if (!empty($file_url) && !empty($file_name)) {
            // Сохранить файл в хранилище
            try {
                $responseFile = $fileStorageService->saveBase64FileToStorage(
                    $file_url,
                    $file_name,
                    $unique_url,
                    $size
                );

                if ($responseFile) {
                    return [
                        'success' => true,
                        'new_url' => $responseFile
                    ];
                }
                else {
                    return [
                        'success' => false,
                        'message' => 'Failed to upload image.',
                        'status_code' => 500
                    ];
                }
            }
            catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'status_code' => 500
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'No data Image',
            'status_code' => 400
        ];
    }

    // Сортировка таблицы по указанному или default полю
    protected function applySorting(
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
                // Сортировка по полю 'to' внутри JSON
                $query->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(period, '$.to')) AS DATETIME) {$direction}");
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

}

