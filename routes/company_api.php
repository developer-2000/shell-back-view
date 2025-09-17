<?php
declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DesignChatController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\PromotionSurfaceController;
use App\Http\Controllers\PromotionSurfaceDesignController;
use App\Http\Controllers\SurfaceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebSocketController;
use App\Http\Controllers\CompanyPlannerController;
use App\Http\Controllers\HistoryPlannerController;
use App\Http\Controllers\XlFileController;
use App\Http\Controllers\UserPromotionController;
use App\Http\Controllers\PrintPromotionReportController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\DistributorController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::middleware('role:user')->group(function () {
        // Company planner surfaces
        Route::apiResources([
            'company-planner' => CompanyPlannerController::class,
        ], ['only' => ['index']]);
        Route::group(['prefix'=>'company-planner'], function (){
            Route::post('save-amount-company-planner', [CompanyPlannerController::class, 'saveAmountCompanyPlanner']);
        });
        // History planner
        Route::apiResources([
            'history-planner' => HistoryPlannerController::class,
        ]);
        // User Promotion
        Route::apiResources([
            'user-promotion' => UserPromotionController::class,
        ]);
        // Users
        Route::group(['prefix'=>'users'], function (){
            Route::get('get-user-company-planner', [UserController::class, 'getUserCompanyPlanner']);
        });
    });
});
