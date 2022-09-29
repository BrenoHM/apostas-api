<?php

use App\Http\Controllers\DepositController;
use App\Http\Controllers\TimesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

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
    return ['Laravel' => app()->version()];
});

Route::get('/times', [TimesController::class, 'index']);

Route::post('/process_payment', [TimesController::class, 'processPayment']);

Route::post('/process_payment_pix', [TimesController::class, 'processPaymentPix']);

Route::post('/process_payment_bill', [TimesController::class, 'processPaymentBill']);

Route::post('/notifications', [NotificationController::class, 'mercadoPago']);

//rora para testar a rotina de processamento de bets
Route::get('/process_bets', [TimesController::class, 'processBets']);

require __DIR__.'/auth.php';