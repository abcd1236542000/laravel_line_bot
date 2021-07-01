<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LineBotController;
use App\Http\Controllers\LineNotificationController;
use App\Http\Controllers\TestController;

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

Route::group([
    'prefix' => 'webhook',
], function () {
    Route::group([
        'prefix' => 'bot',
    ], function () {
        Route::POST('/line', [LineBotController::class, 'webhook']);
    });
});

Route::group([
    'prefix' => 'notification',
], function () {
    Route::GET('/line/authorize-url', [LineNotificationController::class, 'getAuthorizeUrl']);
    Route::GET('/line/notify/authorize-callback', [LineNotificationController::class, 'callbackLineNotifyAuthorize']);
});

Route::group([
    'prefix' => 'test',
], function () {
    Route::GET('/famiport/{ec_order}', [TestController::class, 'famiport']);
});