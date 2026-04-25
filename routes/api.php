<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\OxaPayCallbackController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/oxapay/callback', OxaPayCallbackController::class)
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('api.oxapay.callback');
