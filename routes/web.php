<?php

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Admin\AuthController;

// Route::get('/', function () {
//     return view('welcome');
// });




// Route::get('/admin/login', [AuthController::class, 'showLogin']);
// Route::post('/admin/login', [AuthController::class, 'login']);

// Route::middleware(['admin'])->group(function () {
//     Route::get('/admin/dashboard', fn() => view('admin.dashboard'));
// });

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
})->name('home');

Route::prefix('admin')->group(function () {

    Route::get('/login', [AuthController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

    Route::middleware(['admin'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        Route::get('/profile', [AuthController::class, 'profile'])->name('admin.profile');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('admin.profile.update');
        Route::resource('categories', CategoryController::class)->names('categories');
        Route::resource('products', ProductController::class)->names('products');
        Route::resource('users', UserController::class)->only(['index', 'show'])->names('admin.users');
        Route::get('orders', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');

        Route::get(
            '/payments',
            [PaymentController::class, 'index']
        )->name('admin.payments.index');

        Route::get(
            '/payments/{payment}',
            [PaymentController::class, 'show']
        )->name('admin.payments.show');

        Route::post(
            '/payments/{payment}/approve',
            [PaymentController::class, 'approve']
        )->name('admin.payments.approve');

        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('admin.payments.reject');
    });
});
