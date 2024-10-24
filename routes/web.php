<?php

use Illuminate\Support\Facades\Route;

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
});

//Route::get('/', \App\Http\Controllers\Admin\Post\IndexController::class)->name('category.index');

//Route::get('{page}', \App\Http\Controllers\IndexController::class)->where('page', '.*');

