<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\LedgerController;
use App\Http\Controllers\Api\MerchantsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PointsController;
use App\Http\Controllers\Api\QrController;
use App\Http\Controllers\Api\RewardController as ApiRewardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RewardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::get('categories', [AuthController::class, 'getCategories']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('merchant/profile/create', [AuthController::class, 'createMerchantPage']);
    Route::post('merchant/profile/update', [AuthController::class, 'updateMerchantPage']);

    Route::prefix('profile')->group(function () {
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });

    Route::group(['middleware' => ['subscribed']], function () {
        Route::prefix('points')->group(function () {
            Route::get('balance', [PointsController::class, 'getBalance']);
            Route::post('transfer', [PointsController::class, 'depositBalance']);
        });
    });

    Route::prefix('transactions')->group(function () {
        Route::get('merchant', [LedgerController::class, 'getTransactionsMerchant']);
        Route::get('user', [LedgerController::class, 'getTransactionsUser']);
        Route::get('user-merchant', [LedgerController::class, 'getTransactionsUserMerchant']);
    });

    Route::prefix('merchants')->group(function () {
        Route::get('all', [MerchantsController::class, 'getAllMerchants']);
        Route::get('subscribed', [MerchantsController::class, 'fetchAllSubscribedMerchants']);
        Route::get('available', [MerchantsController::class, 'fetchAllAvailableMerchants']);
        Route::get('{id}', [MerchantsController::class, 'getMerchant']);
        Route::get('{id}/messages', [MerchantsController::class, 'getMerchantMessages']);
        Route::get('{id}/rewards', [MerchantsController::class, 'getMerchantRewards']);
    });

    Route::post('message', [MerchantsController::class, 'createMessage']);
    Route::post('reward', [MerchantsController::class, 'createReward']);
    Route::delete('message/{id}', [MerchantsController::class, 'deleteMessage']);
    Route::delete('reward/{id}', [MerchantsController::class, 'deleteReward']);
    Route::post('message/{id}', [MerchantsController::class, 'updateMessage']);
    Route::post('reward/{id}', [MerchantsController::class, 'updateReward']);
    
    Route::post('notification', [NotificationController::class, 'createNotification']);
    Route::get('notifications', [NotificationController::class, 'getAllNotifications']);
    Route::get('messages', [NotificationController::class, 'getAllMessages']);
    Route::get('rewards', [MerchantsController::class, 'getAllRewards']);

    Route::post('claim', [ApiRewardController::class, 'claimReward']);
    Route::get('claim', [ApiRewardController::class, 'getClaimedRewards']);

    Route::prefix('employees')->group(function () {
        Route::get('', [EmployeeController::class, 'getEmployees']);
        Route::post('', [EmployeeController::class, 'createEmployee']);
        Route::post('/{id}', [EmployeeController::class, 'updateEmployee']);
        Route::delete('/{id}', [EmployeeController::class, 'deleteEmployee']);
    });
});

Route::prefix('qr')->group(function () {
    Route::get('generate/user', [QrController::class, 'generateQrCodeUser']);
    Route::get('generate/reward', [QrController::class, 'generateRewardQr']);
    Route::get('generate/merchant', [QrController::class, 'generateQrCodeMerchant']);
    Route::post('validate/user', [QrController::class, 'validateQrCodeUser']);
    Route::post('validate/merchant', [QrController::class, 'validateQrCodeMerchant']);
});

