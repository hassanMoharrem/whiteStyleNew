<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\SubCategoryController;
use App\Http\Controllers\Api\Admin\BrandController;
use App\Http\Controllers\Api\Admin\SizeController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\FaqController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ColorController as AdminColorController;
use App\Http\Controllers\Api\SabeqController;
use App\Http\Controllers\Api\Site\NavigationController;
use App\Http\Controllers\Api\Site\ColorController as SiteColorController;
use App\Http\Controllers\Api\Site\ProductController as SiteProductController;
use App\Http\Controllers\Api\Site\BrandController as SiteBrandController;
use App\Http\Controllers\Api\Site\ContactController as SiteContactController;
use App\Http\Controllers\Api\Site\CategoryController as SiteCategoryController;
use App\Http\Controllers\Api\Site\OrderController as SiteOrderController;
use App\Http\Controllers\Api\Site\CitiesController;
use App\Http\Controllers\Api\Site\SubscribeController;
use App\Http\Controllers\Api\User\AuthController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Public Site Routes
Route::get('/site/navigation', [NavigationController::class, 'index']);
Route::get('/site/sliders', [NavigationController::class, 'sliders']);
Route::get('/site/sub-categories', [NavigationController::class, 'subCategories']);
Route::get('/site/colors', [SiteColorController::class, 'index']);
Route::get('/site/categories', [SiteCategoryController::class, 'index']);
Route::get('/site/categories/{id}', [SiteCategoryController::class, 'show']);
Route::get('/site/products', [SiteProductController::class, 'index']);
Route::get('/site/products/{id}', [SiteProductController::class, 'show']);
Route::get('/site/brands', [SiteBrandController::class, 'index']);
Route::get('/site/brands/{id}', [SiteBrandController::class, 'show']);
Route::get('/site/brands/{id}/products', [SiteBrandController::class, 'products']);
Route::post('/site/contact', [SiteContactController::class, 'store']);
Route::get('/site/cities', [SiteOrderController::class, 'getCities']);
Route::post('/site/orders', [SiteOrderController::class, 'store']);
Route::get('/site/cities/all', [CitiesController::class, 'index']);
Route::post('/site/subscribe', [SubscribeController::class, 'store']);
Route::get('/sabeq/areas', [SabeqController::class, 'areas']);
Route::get('/sabeq/parcel/{orderId}/{trackNumber}', [SiteOrderController::class, 'informationParcel']);
Route::post('/sabeq/parcel/cancel/{orderId}/{trackNumber}', [SiteOrderController::class, 'cancelParcel']);
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);


    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/me', [AdminAuthController::class, 'me']);

        // user management
        Route::apiResource('users', App\Http\Controllers\Api\Admin\UserController::class);

        // Dashboard Statistics
        Route::get('dashboard/statistics', [DashboardController::class, 'getStatistics']);

        // Sliders
        Route::apiResource('sliders', SliderController::class);
        Route::patch('sliders/{slider}/toggle', [SliderController::class, 'toggleVisible']);

        // Categories
        Route::apiResource('categories', CategoryController::class);
        Route::patch('categories/{category}/toggle', [CategoryController::class, 'toggleVisible']);

        // Sub-categories
        Route::apiResource('sub-categories', SubCategoryController::class);

        // Brands
        Route::apiResource('brands', BrandController::class);

        // Sizes
        Route::apiResource('sizes', SizeController::class);

        // Cities
        Route::apiResource('cities', CityController::class);

        // Products
        Route::apiResource('products', ProductController::class);

        // FAQs
        Route::apiResource('faqs', FaqController::class);
        Route::patch('faqs/{faq}/toggle', [FaqController::class, 'toggleVisible']);

        // Contacts
        Route::get('contacts', [SiteContactController::class, 'index']);
        Route::delete('contacts/{contact}', [SiteContactController::class, 'destroy']);

        // Orders
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
        Route::delete('orders/{order}', [OrderController::class, 'destroy']);
         // Colors
        Route::get('colors', [AdminColorController::class, 'index']);
        Route::put('colors/{color}', [AdminColorController::class, 'update']);
        Route::post('colors/update-all', [AdminColorController::class, 'updateAll']);
        Route::post('colors/reset', [AdminColorController::class, 'reset']);

        // Subscribes
        Route::get('subscribes', [App\Http\Controllers\Api\Admin\SubscribeController::class, 'index']);
        Route::delete('subscribes/{subscribe}', [App\Http\Controllers\Api\Admin\SubscribeController::class, 'destroy']);

    });
});

// Public routes for products and areas (accessible to users for order creation)
Route::get('/products', [SiteProductController::class, 'index']);
Route::get('/sabeq/areas', [SabeqController::class, 'areas']);

Route::prefix('user')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:user')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // User Orders
        Route::get('orders', [App\Http\Controllers\Api\User\OrderController::class, 'index']);
        Route::get('orders/stats', [App\Http\Controllers\Api\User\OrderController::class, 'stats']);
        Route::post('orders', [App\Http\Controllers\Api\User\OrderController::class, 'store']);
        Route::get('orders/{id}', [App\Http\Controllers\Api\User\OrderController::class, 'show']);
        Route::put('orders/{id}', [App\Http\Controllers\Api\User\OrderController::class, 'update']);
        Route::post('orders/{id}/cancel', [App\Http\Controllers\Api\User\OrderController::class, 'cancel']);
        Route::post('orders/{id}/completed', [App\Http\Controllers\Api\User\OrderController::class, 'completed']);
        Route::delete('orders/{id}', [App\Http\Controllers\Api\User\OrderController::class, 'destroy']);
        Route::get('/orders/{id}/print', [App\Http\Controllers\Api\User\OrderController::class, 'print']);
        Route::post('/orders/find-by-track', [App\Http\Controllers\Api\User\OrderController::class, 'findByTrackNumber']);
        Route::post('/orders/bulk-mark-ready', [App\Http\Controllers\Api\User\OrderController::class, 'bulkMarkReady']);

    });
});

