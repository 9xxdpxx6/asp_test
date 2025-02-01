<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Обработка preflight-запросов (OPTIONS)
Route::options('{any}', function (Request $request) {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', $request->header('Origin'))
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE')
        ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization');
})->where('any', '.*');

// Основные маршруты
Route::middleware([EnsureFrontendRequestsAreStateful::class])->get('/user', function (Request $request) {
    return $request->user();
});

Route::fallback(function (Request $request) {
    return response()->json([
        'error' => 'Route not found',
        'path' => $request->path(),
    ], 404);
});

Route::group(['prefix' => 'guest'], function () {
    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', \App\Http\Controllers\General\Category\IndexController::class);
        Route::get('/{category}', \App\Http\Controllers\General\Category\GetController::class);
    });

    Route::group(['prefix' => 'posts'], function () {
        Route::get('/', \App\Http\Controllers\General\Post\IndexController::class);
        Route::get('/{post}', \App\Http\Controllers\General\Post\GetController::class);
    });

    Route::group(['prefix' => 'discounts'], function () {
        Route::get('/', \App\Http\Controllers\General\Discount\IndexController::class);
        Route::get('/{discount}', \App\Http\Controllers\General\Discount\GetController::class);
    });

    Route::get('/visits', \App\Http\Controllers\Stats\Visit\IndexController::class);

    Route::post('/callback-requests', \App\Http\Controllers\General\CallbackRequest\StoreController::class);
});
