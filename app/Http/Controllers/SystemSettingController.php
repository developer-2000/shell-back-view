<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\Surfaces\SurfaceSaveRequest;
use App\Http\Requests\Surfaces\SurfaceUpdateRequest;
use App\Http\Requests\System\UpdateSystemRequest;
use App\Models\Surface;
use App\Models\SystemSetting;
use App\Repositories\SystemSettingRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SystemSettingController extends BaseController {

    protected SystemSettingRepository $systemSettingRepository;

    public function __construct(SystemSettingRepository $systemSettingRepository) {
        $this->systemSettingRepository = $systemSettingRepository;
    }


    public function index(Request $request): JsonResponse {
        $settings = $this->systemSettingRepository->getSettings();

        return $this->getSuccessResponse('', $settings);
    }

    public function store(UpdateSystemRequest $request): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий
        $result = $this->systemSettingRepository->updateSystem($validatedData);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    public function update(UpdateSystemRequest $request, SystemSetting $systemSetting): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий
        $result = $this->systemSettingRepository->updateSystem($validatedData);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

}
