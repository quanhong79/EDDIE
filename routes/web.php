<?php

use Illuminate\Support\Facades\Route;

// Public controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\AuthController;

// User controllers
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\VnpayController;

// Admin controllers
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ThongKeController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/about', fn () => view('about'))->name('about');

// Products & Categories (public)
Route::get('/products', [ProductController::class, 'publicIndex'])->name('product.index');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/c/{category:slug}', [CatalogController::class, 'index'])->name('category.show');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| User (phải đăng nhập)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Settings (tùy bạn sử dụng)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::patch('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::patch('/settings/language', [SettingsController::class, 'updateLanguage'])->name('settings.language.update');
    Route::patch('/settings/payment', [SettingsController::class, 'updatePayment'])->name('settings.payment.update');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');

    // Chat (user)
    Route::get('/chat-ai',         [ChatBotController::class, 'index'])->name('chat.index');
    Route::post('/chat-ai/stream', [ChatBotController::class, 'stream'])->name('chat.stream');   
    Route::get('/chat-ai/messages',[ChatBotController::class, 'messages'])->name('chat.messages');
    Route::get('/chat-ai/unread',  [ChatBotController::class, 'unread'])->name('chat.unread');
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Orders (dùng chung cho user & admin; kiểm tra quyền trong controller)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');   // PATCH
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/orders/{id}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');

    // Reviews (user viết bình luận tại trang product.show)
    Route::post('/product/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index'); // ← đã bỏ dòng trùng
    Route::post('/checkout/cod', [CheckoutController::class, 'cod'])->name('checkout.cod');

    // VietQR nội bộ
    Route::get('/checkout/bank', [CheckoutController::class, 'bankForm'])->name('checkout.bank');
    Route::post('/checkout/vietqr', [CheckoutController::class, 'bankPay'])->name('checkout.vietqr');

    // VNPay
    Route::post('/checkout/vnpay', [VnpayController::class, 'create'])->name('checkout.vnpay');
    Route::get('/checkout/vnpay/return', [VnpayController::class, 'return'])->name('checkout.vnpay.return');
});

/*
|--------------------------------------------------------------------------
| Admin (auth + admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');



        // Products (Admin) => admin.products.*
        Route::resource('products', ProductController::class)->names('products');

        // Categories (Admin) => admin.categories.*
        Route::resource('categories', CategoryController::class)
            ->parameters(['categories' => 'category'])
            ->names('categories');

        // Reviews moderation (Admin)
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('/reviews/{review}/hide', [ReviewController::class, 'hide'])->name('reviews.hide');

        // Thống kê
        Route::get('/thongke', [ThongKeController::class, 'index'])->name('thongke.index');
        Route::get('/thongke/export/{type}', [ThongKeController::class, 'exportExcel'])->name('thongke.export');
    });
