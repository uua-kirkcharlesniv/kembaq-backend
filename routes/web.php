<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
use Bavix\Wallet\Models\Transaction;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('rewards', RewardController::class);
    Route::resource('marketing', MessageController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('users', SubscriptionController::class);
});