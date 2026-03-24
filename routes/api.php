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

Route::group(['prefix' => 'guest'], function () {
    Route::get('/hero', \App\Http\Controllers\General\Hero\GetController::class);

    Route::group(['prefix' => 'advantages'], function () {
        Route::get('/', \App\Http\Controllers\General\Advantage\IndexController::class);
    });

    Route::get('/about', \App\Http\Controllers\General\About\GetController::class);

    Route::get('/contacts', \App\Http\Controllers\General\Contact\GetController::class);

    Route::get('/why-choose-us', \App\Http\Controllers\General\HomeFeature\WhyChooseUsGetController::class);
    Route::get('/learning-process', \App\Http\Controllers\General\HomeFeature\LearningProcessGetController::class);
    Route::get('/callback-section', \App\Http\Controllers\General\HomeFeature\CallbackSectionGetController::class);

    Route::get('/footer', \App\Http\Controllers\General\Footer\GetController::class);
    Route::get('/footer/pdf/{footerDocument}', \App\Http\Controllers\General\Footer\DownloadPdfController::class);

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', \App\Http\Controllers\General\Category\IndexController::class);
        Route::get('/featured', \App\Http\Controllers\General\Category\FeaturedController::class);
        Route::get('/{category}', \App\Http\Controllers\General\Category\GetController::class);
    });

    Route::group(['prefix' => 'posts'], function () {
        Route::get('/', \App\Http\Controllers\General\Post\IndexController::class);
        Route::get('/{post}', \App\Http\Controllers\General\Post\GetController::class);
    });

    Route::group(['prefix' => 'discounts'], function () {
        Route::get('/', \App\Http\Controllers\General\Discount\IndexController::class);
        Route::get('/home', \App\Http\Controllers\General\Discount\HomeController::class);
        Route::get('/{discount}', \App\Http\Controllers\General\Discount\GetController::class);
    });

    Route::group(['prefix' => 'review-widgets'], function () {
        Route::get('/home', \App\Http\Controllers\General\ReviewWidget\HomeController::class);
    });

    Route::get('/visits', \App\Http\Controllers\Stats\Visit\IndexController::class);

    Route::post('/callback-requests', \App\Http\Controllers\General\CallbackRequest\StoreController::class);
});

Route::fallback(function (Request $request) {
    return response()->json([
        'error' => 'Route not found',
        'path' => $request->path(),
    ], 404);
});
