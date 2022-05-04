<?php

use App\Http\Controllers\Api\AuthController;
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
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::prefix('profile')->group(function () {
        // Route::get('', [AuthController::class, 'getOwnProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        // Route::post('update', [AuthController::class, 'updateProfile']);
    });

});

Route::group(['middleware' => ['auth:api']], function () {
    Route::prefix('profile')->group(function () {
        // Route::get('', [AuthController::class, 'getOwnProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        // Route::post('update', [AuthController::class, 'updateProfile']);
    });
});
Route::group(['middleware' => ['subscribed']], function () {
    Route::prefix('points')->group(function () {
        Route::post('balance', [PointsController::class, 'getBalance']);
        Route::post('transfer', [PointsController::class, 'depositBalance']);
    });
});

Route::prefix('qr')->group(function () {
    Route::post('generate/user', [QrController::class, 'generateQrCodeUser']);
    Route::post('generate/merchant', [QrController::class, 'generateQrCodeMerchant']);
    Route::post('validate/user', [QrController::class, 'validateQrCodeUser']);
    Route::post('validate/merchant', [QrController::class, 'validateQrCodeMerchant']);
});

