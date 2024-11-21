<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', \App\Http\Controllers\General\Post\IndexController::class)->name('main.index');


//Auth::routes();

Route::middleware('web')->get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

Route::group(['prefix' => 'admin'], function () {
    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', \App\Http\Controllers\Admin\Category\IndexController::class)->name('category.index');
        Route::get('/create', \App\Http\Controllers\Admin\Category\CreateController::class)->name('category.create');
        Route::post('/', \App\Http\Controllers\Admin\Category\StoreController::class)->name('category.store');
        Route::get('/{category}/edit', \App\Http\Controllers\Admin\Category\EditController::class)->name('category.edit');
        Route::get('/{category}', \App\Http\Controllers\Admin\Category\ShowController::class)->name('category.show');
        Route::patch('/{category}', \App\Http\Controllers\Admin\Category\UpdateController::class)->name('category.update');
        Route::delete('/{category}', \App\Http\Controllers\Admin\Category\DeleteController::class)->name('category.delete');
    });

    Route::group(['prefix' => 'posts'], function () {
        Route::get('/', \App\Http\Controllers\Admin\Post\IndexController::class)->name('post.index');
        Route::get('/create', \App\Http\Controllers\Admin\Post\CreateController::class)->name('post.create');
        Route::post('/', \App\Http\Controllers\Admin\Post\StoreController::class)->name('post.store');
        Route::get('/{post}/edit', \App\Http\Controllers\Admin\Post\EditController::class)->name('post.edit');
        Route::get('/{post}', \App\Http\Controllers\Admin\Post\ShowController::class)->name('post.show');
        Route::patch('/{post}', \App\Http\Controllers\Admin\Post\UpdateController::class)->name('post.update');
        Route::delete('/{post}', \App\Http\Controllers\Admin\Post\DeleteController::class)->name('post.delete');
    });

    Route::group(['prefix' => 'discounts'], function () {
        Route::get('/', \App\Http\Controllers\Admin\Discount\IndexController::class)->name('discount.index');
        Route::get('/create', \App\Http\Controllers\Admin\Discount\CreateController::class)->name('discount.create');
        Route::post('/', \App\Http\Controllers\Admin\Discount\StoreController::class)->name('discount.store');
        Route::get('/{discount}/edit', \App\Http\Controllers\Admin\Discount\EditController::class)->name('discount.edit');
        Route::get('/{discount}', \App\Http\Controllers\Admin\Discount\ShowController::class)->name('discount.show');
        Route::patch('/{discount}', \App\Http\Controllers\Admin\Discount\UpdateController::class)->name('discount.update');
        Route::delete('/{discount}', \App\Http\Controllers\Admin\Discount\DeleteController::class)->name('discount.delete');
    });

    Route::group(['prefix' => 'callback-requests'], function () {
        Route::get('/', \App\Http\Controllers\Admin\CallbackRequest\IndexController::class)->name('callback.index');
        Route::get('/create', \App\Http\Controllers\Admin\CallbackRequest\CreateController::class)->name('callback.create');
        Route::post('/', \App\Http\Controllers\Admin\CallbackRequest\StoreController::class)->name('callback.store');
        Route::get('/{callback}/edit', \App\Http\Controllers\Admin\CallbackRequest\EditController::class)->name('callback.edit');
        Route::get('/{callback}', \App\Http\Controllers\Admin\CallbackRequest\ShowController::class)->name('callback.show');
        Route::patch('/{callback}', \App\Http\Controllers\Admin\CallbackRequest\UpdateController::class)->name('callback.update');
        Route::delete('/{callback}', \App\Http\Controllers\Admin\CallbackRequest\DeleteController::class)->name('callback.delete');
    });
});

Route::get('/', \App\Http\Controllers\IndexController::class)->name('main.index');
Route::get('{page}', \App\Http\Controllers\IndexController::class)->where('page', '.*');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
