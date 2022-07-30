<?php

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/payment/{modal}/paypal', [PaymentController::class, 'create']);
});
Route::get('/payment-success', [PaymentController::class, 'success'])->name('paypal-payment-success');