<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;


/*
|----------------------------------------------------------------------
| Web Routes
|----------------------------------------------------------------------
*/

// CSRF Cookie route for Sanctum authentication
Route::middleware('web')->get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

// Admin routes with authentication middleware
Route::group(['prefix' => 'admin', 'middleware' => 'web'], function () {

    // Disable registration and reset password routes
    Auth::routes(['register' => false, 'reset' => false]);

    // Authentication routes inside admin
    Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('admin.logout');

    // Admin routes that require authentication
    Route::group(['middleware' => 'auth'], function () {

        // Categories routes
        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', \App\Http\Controllers\Admin\Category\IndexController::class)->name('category.index');
            Route::get('/create', \App\Http\Controllers\Admin\Category\CreateController::class)->name('category.create');
            Route::post('/', \App\Http\Controllers\Admin\Category\StoreController::class)->name('category.store');
            Route::get('/{category}/edit', \App\Http\Controllers\Admin\Category\EditController::class)->name('category.edit');
            Route::get('/{category}', \App\Http\Controllers\Admin\Category\ShowController::class)->name('category.show');
            Route::patch('/{category}', \App\Http\Controllers\Admin\Category\UpdateController::class)->name('category.update');
            Route::delete('/{category}', \App\Http\Controllers\Admin\Category\DeleteController::class)->name('category.delete');
        });

        // Posts routes
        Route::group(['prefix' => 'posts'], function () {
            Route::get('/', \App\Http\Controllers\Admin\Post\IndexController::class)->name('post.index');
            Route::get('/create', \App\Http\Controllers\Admin\Post\CreateController::class)->name('post.create');
            Route::post('/', \App\Http\Controllers\Admin\Post\StoreController::class)->name('post.store');
            Route::get('/{post}/edit', \App\Http\Controllers\Admin\Post\EditController::class)->name('post.edit');
            Route::get('/{post}', \App\Http\Controllers\Admin\Post\ShowController::class)->name('post.show');
            Route::patch('/{post}', \App\Http\Controllers\Admin\Post\UpdateController::class)->name('post.update');
            Route::delete('/{post}', \App\Http\Controllers\Admin\Post\DeleteController::class)->name('post.delete');
        });

        // Discounts routes
        Route::group(['prefix' => 'discounts'], function () {
            Route::get('/', \App\Http\Controllers\Admin\Discount\IndexController::class)->name('discount.index');
            Route::get('/create', \App\Http\Controllers\Admin\Discount\CreateController::class)->name('discount.create');
            Route::post('/', \App\Http\Controllers\Admin\Discount\StoreController::class)->name('discount.store');
            Route::get('/home-section', [\App\Http\Controllers\Admin\Discount\HomeSectionController::class, 'index'])->name('admin.discounts.home-section');
            Route::post('/home-section', [\App\Http\Controllers\Admin\Discount\HomeSectionController::class, 'update'])->name('admin.discounts.home-section.update');
            Route::get('/discount-order', [\App\Http\Controllers\Admin\DiscountOrderController::class, 'index'])->name('admin.discount-order');
            Route::post('/discount-order', [\App\Http\Controllers\Admin\DiscountOrderController::class, 'update'])->name('admin.discount-order.update');
            Route::get('/{discount}/edit', \App\Http\Controllers\Admin\Discount\EditController::class)->name('discount.edit');
            Route::get('/{discount}', \App\Http\Controllers\Admin\Discount\ShowController::class)->name('discount.show');
            Route::patch('/{discount}', \App\Http\Controllers\Admin\Discount\UpdateController::class)->name('discount.update');
            Route::delete('/{discount}', \App\Http\Controllers\Admin\Discount\DeleteController::class)->name('discount.delete');
        });

        // Callback Requests routes
        Route::group(['prefix' => 'callback-requests'], function () {
            Route::get('/', \App\Http\Controllers\Admin\CallbackRequest\IndexController::class)->name('callback.index');
            Route::get('/create', \App\Http\Controllers\Admin\CallbackRequest\CreateController::class)->name('callback.create');
            Route::post('/', \App\Http\Controllers\Admin\CallbackRequest\StoreController::class)->name('callback.store');
            Route::get('/{callback}/edit', \App\Http\Controllers\Admin\CallbackRequest\EditController::class)->name('callback.edit');
            Route::get('/{callback}', \App\Http\Controllers\Admin\CallbackRequest\ShowController::class)->name('callback.show');
            Route::patch('/{callback}', \App\Http\Controllers\Admin\CallbackRequest\UpdateController::class)->name('callback.update');
            Route::delete('/{callback}', \App\Http\Controllers\Admin\CallbackRequest\DeleteController::class)->name('callback.delete');
        });

        // Featured categories on homepage
        Route::get('/featured-categories', [\App\Http\Controllers\Admin\FeaturedCategoriesController::class, 'index'])->name('admin.featured-categories');
        Route::post('/featured-categories', [\App\Http\Controllers\Admin\FeaturedCategoriesController::class, 'update'])->name('admin.featured-categories.update');
        Route::get('/advantages', [\App\Http\Controllers\Admin\AdvantagesController::class, 'index'])->name('admin.advantages');
        Route::post('/advantages', [\App\Http\Controllers\Admin\AdvantagesController::class, 'update'])->name('admin.advantages.update');
        Route::get('/about-page', [\App\Http\Controllers\Admin\AboutPageController::class, 'index'])->name('admin.about-page');
        Route::post('/about-page', [\App\Http\Controllers\Admin\AboutPageController::class, 'update'])->name('admin.about-page.update');
        Route::get('/contacts-page', [\App\Http\Controllers\Admin\ContactPageController::class, 'index'])->name('admin.contacts-page');
        Route::post('/contacts-page', [\App\Http\Controllers\Admin\ContactPageController::class, 'update'])->name('admin.contacts-page.update');
        Route::get('/hero-settings', [\App\Http\Controllers\Admin\HeroSettingsController::class, 'index'])->name('admin.hero-settings');
        Route::post('/hero-settings', [\App\Http\Controllers\Admin\HeroSettingsController::class, 'update'])->name('admin.hero-settings.update');
        Route::get('/why-choose-us', [\App\Http\Controllers\Admin\WhyChooseUsController::class, 'index'])->name('admin.why-choose-us');
        Route::post('/why-choose-us', [\App\Http\Controllers\Admin\WhyChooseUsController::class, 'update'])->name('admin.why-choose-us.update');
        Route::get('/learning-process', [\App\Http\Controllers\Admin\LearningProcessController::class, 'index'])->name('admin.learning-process');
        Route::post('/learning-process', [\App\Http\Controllers\Admin\LearningProcessController::class, 'update'])->name('admin.learning-process.update');
        Route::get('/callback-section', [\App\Http\Controllers\Admin\CallbackSectionController::class, 'index'])->name('admin.callback-section');
        Route::post('/callback-section', [\App\Http\Controllers\Admin\CallbackSectionController::class, 'update'])->name('admin.callback-section.update');
        Route::get('/reviews-builder', [\App\Http\Controllers\Admin\ReviewWidgetController::class, 'index'])->name('admin.reviews-builder');
        Route::post('/reviews-builder', [\App\Http\Controllers\Admin\ReviewWidgetController::class, 'update'])->name('admin.reviews-builder.update');
        Route::get('/footer-settings', [\App\Http\Controllers\Admin\FooterSettingsController::class, 'index'])->name('admin.footer-settings');
        Route::post('/footer-settings', [\App\Http\Controllers\Admin\FooterSettingsController::class, 'update'])->name('admin.footer-settings.update');
        Route::get('/site-lab/reviews-layout-7f3k2m', function () {
            return redirect()->route('admin.reviews-builder');
        });
        Route::get('/category-order', [\App\Http\Controllers\Admin\CategoryOrderController::class, 'index'])->name('admin.category-order');
        Route::post('/category-order', [\App\Http\Controllers\Admin\CategoryOrderController::class, 'update'])->name('admin.category-order.update');

        Route::get('/', function () {
            return redirect()->route('category.index');
        })->name('admin.dashboard');
    });

});

// Client routes (for all URLs outside /admin/)
//Route::get('/', \App\Http\Controllers\IndexController::class)->name('main.index');
Route::get('{page}', \App\Http\Controllers\IndexController::class)->where('page', '.*');
