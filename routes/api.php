<?php

use App\Http\Controllers\BetController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TimesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/matches/today', [TimesController::class, 'matchesToday']);

Route::post('/process_payment', [TimesController::class, 'processPayment']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/matches', [TimesController::class, 'matches']);
    //Route::post('/bets', [BetController::class, 'store'])->middleware(['auth:sanctum']);
    Route::prefix('bets')->group(function() {
        Route::get('/', [BetController::class, 'index']);
        Route::post('/', [BetController::class, 'store']);
        // Route::get('/{banner_id}', 'BannerController@edit');
        // Route::put('/{banner_id}', 'BannerController@update');
        // Route::delete('/{banner_id}', 'BannerController@destroy');
    });
    Route::post('/deposit', [DepositController::class, 'store']);
});

Route::post('/notifications', [NotificationController::class, 'mercadoPago']);

Route::get('/test-log', function() {
    Log::info("Teste de log");
    return ['ok'];
});