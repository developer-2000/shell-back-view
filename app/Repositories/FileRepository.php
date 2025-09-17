<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;


class FileRepository extends BaseRepository {

    /**
     * Сохранить картинку (новая и замена предыдущей)
     * @param array $validated
     * @param $model
     * @return array
     */
    public function setImage(array $validated, $model) {
        try {
            // Обновление картинки в хранилище
            $uploadResult = $this->processImageUpload($validated, get_class($model));

            if (!$uploadResult['success']) {
                return [
                    'success' => false,
                    'message' => $uploadResult['message'],
                    'status_code' => $uploadResult['status_code']
                ];
            }

            // Обновляем поля модели динамически
            $model->url_images = $uploadResult['image_urls'];
            $model->save();

            return [
                'success' => true,
                'message' => 'Image updated successfully!',
                'status_code' => 200
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
     * Скачать файл из хранилища
     *
     * @param array $validated
     * @return array
     */
    public function downloadFile(array $validated) {
        $tableName = $validated['table_name'];
        $table_id = $validated['table_id'];
        $message_id = $validated['message_id'];

        // Проверяем, существует ли таблица в базе данных
        if (!Schema::hasTable($tableName)) {
            return [
                'success' => false,
                'message' => 'Table does not exist: ' . $tableName,
                'status_code' => 400
            ];
        }

        // Выполняем запрос к указанной таблице с проверкой наличия записи
        $record = DB::table($tableName)
            ->where('id', $table_id)
            ->first();

        if (!$record) {
            return [
                'success' => false,
                'message' => 'Record with such ID not found in table: ' . $table_id,
                'status_code' => 404
            ];
        }

        $url_file = null;

        if($tableName === "design_chats"){
            $messages = json_decode($record->messages, true);
            // Ищем сообщение с указанным message_id
            $message = collect($messages)->firstWhere('id', $message_id);
            // Проверяем, существует ли сообщение и содержит ли оно 'url_file'
            if ($message && isset($message['url_images'])) {
                $url_file = $message['url_images']['original'];
            }
            else {
                return [
                    'success' => false,
                    'message' => "Message or file URL not found:" . $message_id,
                    'status_code' => 404
                ];
            }
        }
        else if($tableName === "promotion_surface_designs"){
            $data = json_decode($record->data, true);

            // Проверяем, что свойство 'files' существует и это массив
            if (isset($data['files']) && is_array($data['files'])) {
                // Ищем файл с указанным message_id
                $file = collect($data['files'])->firstWhere('id', $message_id);

                // Проверяем, существует ли файл и содержит ли он 'url'
                if ($file && isset($file['url'])) {
                    $url_file = $file['url'];
                }
                else {
                    return [
                        'success' => false,
                        'message' => "File not found in data",
                        'status_code' => 404
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => "Files not found in data",
                    'status_code' => 404
                ];
            }
        }

        if (is_null($url_file)) {
            return [
                'success' => false,
                'message' => "The file url is not defined!",
                'status_code' => 400
            ];
        }

        try {
            // Загружаем файл с удаленного хранилища
            $response = Http::get($url_file);

            // Проверяем статус ответа и логируем тело ответа для успешных загрузок
            if ($response->successful()) {
                // Получаем тип контента и имя файла
                $contentType = $response->header('Content-Type');
                $fileName = basename($url_file);

                return [
                    'success' => true,
                    'file' => base64_encode($response->body()),  // Кодируем файл в base64
                    'fileName' => $fileName,  // Отправляем имя файла
                    'contentType' => $contentType,  // Отправляем тип контента
                ];
            }
            else {
                return [
                    'success' => false,
                    'message' => "File download failed",
                    'status_code' => 500
                ];
            }
        }
        catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error exit : ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

}
