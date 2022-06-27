<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LedgerController;
use App\Http\Controllers\Api\MerchantsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PointsController;
use App\Http\Controllers\Api\QrController;
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

    Route::prefix('profile')->group(function () {
        // Route::get('', [AuthController::class, 'getOwnProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        // Route::post('update', [AuthController::class, 'updateProfile']);
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
        Route::get('{id}/messages', [MerchantsController::class, 'getMerchantMessages']);
        Route::get('{id}/rewards', [MerchantsController::class, 'getMerchantRewards']);
    });

    Route::get('notifications', [NotificationController::class, 'getAllNotifications']);
    Route::get('messages', [NotificationController::class, 'getAllMessages']);
});

Route::prefix('qr')->group(function () {
    Route::get('generate/user', [QrController::class, 'generateQrCodeUser']);
    Route::get('generate/merchant', [QrController::class, 'generateQrCodeMerchant']);
    Route::post('validate/user', [QrController::class, 'validateQrCodeUser']);
    Route::post('validate/merchant', [QrController::class, 'validateQrCodeMerchant']);
});

