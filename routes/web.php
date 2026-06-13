<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\EmployeeAuthController;
use App\Http\Controllers\Auth\StaffAuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Menu
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{product:slug}', [MenuController::class, 'show'])->name('menu.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// Order
Route::get('/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/success', [OrderController::class, 'success'])->name('order.success');
Route::get('/order/track', [OrderController::class, 'track'])->name('order.track');


// ============ AUTH: PELANGGAN (No HP + OTP) ============
Route::prefix('login')->name('customer.')->group(function () {
    Route::get('/', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/send-otp', [CustomerAuthController::class, 'sendOtp'])->name('send-otp');
    Route::get('/otp', [CustomerAuthController::class, 'showOtpForm'])->name('otp.form');
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOtp'])->name('verify-otp');
});
Route::post('/logout-customer', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// ============ AUTH: KARYAWAN ============
Route::prefix('karyawan')->name('employee.')->group(function () {
    Route::get('/register', [EmployeeAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [EmployeeAuthController::class, 'register'])->name('register.store');
    Route::get('/login', [EmployeeAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [EmployeeAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [EmployeeAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'role:karyawan'])->group(function () {
        Route::get('/dashboard', fn() => view('employee.dashboard'))->name('dashboard');
    });
});

// ============ AUTH: STAFF (Admin/Bos/IT) ============
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/login', [StaffAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [StaffAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout');
});

// ============ DASHBOARD PER ROLE ============
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
});

Route::middleware(['auth', 'role:bos'])->prefix('bos')->name('bos.')->group(function () {
    Route::get('/dashboard', fn() => view('bos.dashboard'))->name('dashboard');
});

Route::middleware(['auth', 'role:it'])->prefix('it')->name('it.')->group(function () {
    Route::get('/dashboard', fn() => view('it.dashboard'))->name('dashboard');
});