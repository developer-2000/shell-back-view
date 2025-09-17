<?php

namespace App\Http\Controllers;

use App\Http\Requests\XlFile\MakeFileRequest;
use App\Models\PrintPromotionReport;
use App\Services\XlFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class XlFileController extends BaseController {

    public function makeFile(MakeFileRequest $request): JsonResponse {
        $validatedData = $request->validated();
        $xlFileService = new XlFileService($validatedData);
        $xlFileService->generateXLDate();
        $result = $xlFileService->makeFile();

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

}

