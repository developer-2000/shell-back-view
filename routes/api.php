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
use App\Http\Controllers\XlFileController;
use App\Http\Controllers\PrintPromotionReportController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\DistributorController;
use \App\Http\Controllers\FeedbackMessagesController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Role 1
    Route::middleware('role:admin')->group(function () {
        // SystemSetting
        Route::apiResources([
            'system-setting' => SystemSettingController::class,
        ], ['only' => ['index', 'store', 'update']]);
    });
    // Role 1
    Route::middleware('role:admin,cm,cm-admin,designer')->group(function () {
        // Design
        Route::apiResources([
            'designs' => DesignController::class,
        ]);
        Route::group(['prefix'=>'designs'], function (){
            Route::post('get-all-designs', [DesignController::class, 'getAllDesigns']);
        });
        // Products
        Route::apiResources([
            'products' => ProductController::class,
        ], ['only' => ['index', 'store', 'update', 'destroy']]);
        Route::group(['prefix'=>'products'], function (){
            Route::get('get-all-products', [ProductController::class, 'getAllProducts']);
        });
        // Promotion-Surfaces
        Route::apiResources([
            'promotion-surfaces' => PromotionSurfaceController::class,
        ]);
        Route::group(['prefix'=>'promotion-surfaces'], function (){
            Route::delete('{promotion_id}/{surface_id}', [PromotionSurfaceController::class, 'destroy']);
            Route::put('{from_promotion_id}/{whom_promotion_id}', [PromotionSurfaceController::class, 'update']);
        });
        // Promotion-Surfaces-Design
        Route::apiResources([
            'promotion-surface-design' => PromotionSurfaceDesignController::class,
        ], ['only' => ['index', 'store', 'destroy', 'update']]);
        Route::group(['prefix'=>'promotion-surface-design'], function (){
            Route::get('get-promotion-surface-design', [PromotionSurfaceDesignController::class, 'getPromotionSurfaceDesign']);
            Route::post('add-product-brief-design', [PromotionSurfaceDesignController::class, 'addProductBriefDesign']);
            Route::post('delete-product-brief-design', [PromotionSurfaceDesignController::class, 'deleteProductBriefDesign']);
            Route::post('set-files-brief-design', [PromotionSurfaceDesignController::class, 'setFilesBriefDesign']);
            Route::post('delete-file-brief-design', [PromotionSurfaceDesignController::class, 'deleteFileBriefDesign']);
            Route::post('sending-notification-printers', [PromotionSurfaceDesignController::class, 'sendingNotificationPrinters']);
            Route::post('bind-design-to-yourself', [PromotionSurfaceDesignController::class, 'bindDesignToYourself']);
        });
        // Design-Chat
        Route::apiResources([
            'design-chat' => DesignChatController::class,
        ], ['only' => ['index', 'store']]);
        Route::group(['prefix'=>'design-chat'], function (){
            Route::get('delete-message', [DesignChatController::class, 'deleteMessage']);
            Route::post('update-message', [DesignChatController::class, 'updateMessage']);
            Route::post('set-read-status-messages', [DesignChatController::class, 'setReadStatusMessages']);
            Route::post('update-switch-rating-image', [DesignChatController::class, 'updateSwitchRatingImage']);
        });
        // XL File
        Route::group(['prefix'=>'xl-file'], function (){
            Route::post('make-file', [XlFileController::class, 'makeFile']);
        });
        // Web Socket
        Route::group(['prefix'=>'web-socket'], function (){
            Route::get('get-reverb-data', [WebSocketController::class, 'getReverbData']);
        });
    });
    // Role 2
    Route::middleware('role:admin,cm,cm-admin')->group(function () {
        // Categories
        Route::apiResources([
            'categories' => CategoryController::class,
        ]);
        // Surfaces
        Route::apiResources([
            'surfaces' => SurfaceController::class,
        ]);
        Route::group(['prefix'=>'surfaces'], function (){
            Route::post('create-type-surface', [SurfaceController::class, 'createTypeSurface']);
            Route::post('create-size-surface', [SurfaceController::class, 'createSizeSurface']);
            Route::post('get-all-surfaces', [SurfaceController::class, 'getAllSurfaces']);
            Route::post('create-clone-surface', [SurfaceController::class, 'createCloneSurface']);
        });
    });
    // Role 3
    Route::middleware('role:admin,cm,cm-admin,designer,user')->group(function () {
        // Users
        Route::apiResources([
            'users' => UserController::class,
        ], ['only' => ['index','store','update','destroy']]);
        Route::group(['prefix'=>'users'], function (){
            Route::post('update-password', [UserController::class, 'updatePassword']);
            Route::post('get-users-by-field', [UserController::class, 'getUsersByField']);
            Route::post('get-users-ids', [UserController::class, 'getUsersIds']);
            Route::post('get-manager-category', [UserController::class, 'getManagerCategory']);
            Route::post('get-all-roles', [UserController::class, 'getAllRoles']);
        });
    });
    // Role 4
    Route::middleware('role:admin,cm,cm-admin,designer,printer,distributor,user')->group(function () {
        // Promotions
        Route::group(['prefix'=>'promotions'], function (){
            Route::get('promotion-report-view', [PromotionController::class, 'promotionReportView']);
            Route::get('get-status-printer-parcels', [PromotionController::class, 'getStatusPrinterParcels']);
            Route::get('get-status-distributor-parcels', [PromotionController::class, 'getStatusDistributorParcels']);
            Route::get('get-all-cm-i', [PromotionController::class, 'getAllCmAndI']);
            Route::post('notify-admin-about-promotion', [PromotionController::class, 'notifyAdminAboutPromotion']);
        });
        Route::apiResources([
            'promotions' => PromotionController::class,
        ], ['only' => ['index','show','store','update','destroy']]);
        // Files
        Route::group(['prefix'=>'files'], function (){
            Route::post('{type}/{id}/set-image', [FileController::class, 'setImage']);
            Route::get('download-file', [FileController::class, 'downloadFile']);
        });
    });
    // Role 5
    Route::middleware('role:printer')->group(function () {
        // PrintPromotionReportController
        Route::group(['prefix'=>'print-promotion-report'], function (){
            Route::get('get-report', [PrintPromotionReportController::class, 'getReport']);
            Route::post('set-printed', [PrintPromotionReportController::class, 'setPrinted']);
        });
    });
    // Role 6
    Route::middleware('role:distributor')->group(function () {
        // DistributorController
        Route::group(['prefix'=>'promotion-distributor'], function (){
            Route::get('get-promotions', [DistributorController::class, 'getPromotions']);
            Route::post('set-distributor-tracker', [DistributorController::class, 'setDistributorTracker']);
        });
    });
    // Role 7
    Route::middleware('role:user,admin,cm-admin')->group(function () {
        // Auth
        Route::group(['prefix'=>'auth'], function (){
            Route::post('to-re-login', [AuthController::class, 'toReLogin']);
            Route::post('back-re-login', [AuthController::class, 'backReLogin']);
        });
    });
    // feedback-messages
    Route::group(['prefix'=>'feedback-messages'], function (){
        Route::get('get-initialization-data', [FeedbackMessagesController::class, 'getInitializationData']);
        Route::get('get-feedback-data', [FeedbackMessagesController::class, 'getFeedbackData']);
        Route::post('add-message', [FeedbackMessagesController::class, 'addMessage']);
    });
    Route::apiResources([
        'feedback-messages' => FeedbackMessagesController::class,
    ], ['only' => ['index','store']]);

});
