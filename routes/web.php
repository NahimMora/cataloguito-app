<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocalMailController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Models\Shop;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');
Route::view('/legal/privacidad', 'legal', ['kind' => 'privacy'])->name('privacy');
Route::view('/legal/terminos', 'legal', ['kind' => 'terms'])->name('terms');
Route::get('/dev/mail', [LocalMailController::class, 'index'])->name('local.mail');
Route::get('/media/{id}/{size}', [CatalogController::class, 'media'])->whereNumber(['id', 'size'])->name('media.show');
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => app(AuthController::class)->form())->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', fn () => app(AuthController::class)->form('register'))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,10');
    Route::get('/forgot-password', fn () => app(AuthController::class)->form('forgot'))->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgot'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->middleware('throttle:6,1')->name('password.update');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/verify-email', [AuthController::class, 'verifyNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resend'])->middleware('throttle:3,1')->name('verification.send');
    Route::get('/onboarding', [ShopController::class, 'onboarding'])->name('onboarding');
    Route::post('/onboarding', [ShopController::class, 'create'])->middleware('throttle:6,1');
    Route::prefix('app')->middleware('shop')->group(function () {
        Route::get('/', [ShopController::class, 'dashboard'])->name('dashboard');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
        Route::get('/products/new', [ProductController::class, 'form'])->name('products.create');
        Route::post('/products', [ProductController::class, 'save'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'form'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'save'])->name('products.update');
        Route::patch('/products/{id}/quick', [ProductController::class, 'quick'])->name('products.quick');
        Route::post('/products/{id}/archive', [ProductController::class, 'archive'])->name('products.archive');
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'save'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'save'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'delete'])->name('categories.delete');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{id}/status', [OrderController::class, 'status'])->name('orders.status');
        Route::get('/catalog', [ShopController::class, 'settings'])->name('shop.settings');
        Route::put('/catalog', [ShopController::class, 'update'])->name('shop.update');
        Route::put('/appearance', [ShopController::class, 'appearance'])->name('shop.appearance');
        Route::post('/publish', [ShopController::class, 'publish'])->name('shop.publish');
        Route::get('/preview', [CatalogController::class, 'preview'])->name('shop.preview');
        Route::get('/account', [ShopController::class, 'account'])->name('account');
        Route::put('/account', [ShopController::class, 'accountUpdate'])->name('account.update');
        Route::delete('/account', [ShopController::class, 'delete'])->name('account.delete');
    });
});
Route::get('/sitemap.xml', function () {
    return response()->view('sitemap', ['shops' => Shop::where('status', 'published')->get()])->header('Content-Type', 'application/xml');
});
Route::get('/{slug}/p/{id}/{title?}', [CatalogController::class, 'product'])->name('catalog.product');
Route::get('/{slug}/c/{category}/{categorySlug?}', [CatalogController::class, 'index'])->name('catalog.category');
Route::post('/{slug}/quote', [CatalogController::class, 'quote'])->middleware('throttle:quotes')->name('catalog.quote');
Route::post('/{slug}/orders', [CatalogController::class, 'order'])->middleware('throttle:orders')->name('catalog.order');
Route::get('/{slug}/solicitud/{id}', [CatalogController::class, 'receipt'])->name('catalog.receipt');
Route::get('/{slug}/whatsapp',[CatalogController::class, 'whatsapp'])->middleware('throttle:30,1')->name('catalog.whatsapp');
Route::get('/{slug}',[CatalogController::class, 'index'])->name('catalog');
