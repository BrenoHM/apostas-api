<?php

use App\Http\Controllers\BetController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TimesController;
use App\Http\Controllers\MatchController;
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

Route::post('/process_payment', [TimesController::class, 'processPayment']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::prefix('matches')->group(function() {
        Route::get('/', [MatchController::class, 'index']);
        Route::get('/today', [MatchController::class, 'matchesToday']);
    });

    Route::get('/leagues', [LeagueController::class, 'index']);

    Route::prefix('bets')->group(function() {
        Route::get('/', [BetController::class, 'index']);
        Route::post('/', [BetController::class, 'store']);
    });

    Route::post('/deposit', [DepositController::class, 'store']);
    
});

Route::get('/test-log', function() {
    Log::info("Teste de log");
    return ['ok'];
});

Route::get('/statefull', function() {
    return config('sanctum.stateful');
});