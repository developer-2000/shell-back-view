<?php

namespace App\Http\Controllers;

use App\Http\Requests\Files\DownloadFileRequest;
use App\Http\Requests\Files\SetImageRequest;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Surface;
use App\Repositories\FileRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class FileController extends BaseController {

    protected FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository) {
        $this->fileRepository = $fileRepository;
    }

    /**
     * Сохранить картинку указанной модели (новая и замена предыдущей)
     * @param SetImageRequest $request
     * @param $type
     * @param $id
     * @return JsonResponse
     */
    public function setImage(SetImageRequest $request, $type, $id): JsonResponse {
        $validatedData = $request->validated();

        switch ($type) {
            case 'products':
                $model = Product::findOrFail($id);
                break;

            case 'surfaces':
                $model = Surface::findOrFail($id);
                break;

            case 'promotions':
                $model = Promotion::findOrFail($id);
                break;

            default:
                return $this->getErrorResponse('Invalid type', [], 400);
        }

        // Добавляем id модели в массив $validated
        $validatedData['id'] = $id;

        $result = $this->fileRepository->setImage($validatedData, $model);

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Скачать файл из хранилища
     * @param Request $request
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|JsonResponse|Response
     */
    public function downloadFile(DownloadFileRequest $request) {
        $validatedData = $request->validated();

        $result = $this->fileRepository->downloadFile($validatedData);

        if ($result['success']) {
            return $this->getSuccessResponse('', $result);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

}
