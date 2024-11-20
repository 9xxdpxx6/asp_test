<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware([EnsureFrontendRequestsAreStateful::class])->get('/user', function (Request $request) {
    return $request->user();
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

    Route::post('/callback-requests', \App\Http\Controllers\General\CallbackController::class);
});
