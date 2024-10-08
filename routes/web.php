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
Route::get('/categories',\App\Http\Controllers\Admin\Category\IndexController::class)->name('category.index');
Route::get('/categories/create',\App\Http\Controllers\Admin\Category\CreateController::class)->name('category.create');

Route::post('/categories', \App\Http\Controllers\Admin\Category\StoreController::class)->name('category.store');
Route::get('/categories/{category}', \App\Http\Controllers\Admin\Category\ShowController::class)->name('category.show');
Route::get('/categories/{category}/edit', \App\Http\Controllers\Admin\Category\EditController::class)->name('category.edit');
Route::patch('/categories/{category}', \App\Http\Controllers\Admin\Category\UpdateController::class)->name('category.update');
Route::delete('/categories/{category}', \App\Http\Controllers\Admin\Category\DeleteController::class)->name('category.delete');


//Route::patch('/categories/{category}', \App\Http\Controllers\Admin\Category\UpdateController::class)->name('category.update');

//Route::get('{page}', \App\Http\Controllers\General\Post\IndexController::class)->where('page', '.*');

