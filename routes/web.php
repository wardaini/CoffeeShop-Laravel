<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\EmployeeAuthController;
use App\Http\Controllers\Auth\StaffAuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Employee\SalaryController;
use App\Http\Controllers\Employee\DeliveryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\EmployeeVerificationController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Bos\DashboardController as BosDashboard;
use App\Http\Controllers\Bos\ReportController;
use App\Http\Controllers\IT\UserManagementController;
use App\Http\Controllers\IT\AttendanceManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\SalaryManagementController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Bos\SalaryApprovalController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;

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
Route::get('/order/{order}/receipt', [OrderController::class, 'receipt'])->name('order.receipt');

// ============ AUTH: KARYAWAN ============
Route::prefix('karyawan')->name('employee.')->group(function () {
    Route::get('/register', [EmployeeAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [EmployeeAuthController::class, 'register'])->name('register.store');
    Route::get('/login', [EmployeeAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [EmployeeAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [EmployeeAuthController::class, 'logout'])->name('logout');

    Route::get('/cuti', [EmployeeLeaveController::class, 'index'])->name('leave.index');
    Route::get('/cuti/ajukan', [EmployeeLeaveController::class, 'create'])->name('leave.create');
    Route::post('/cuti', [EmployeeLeaveController::class, 'store'])->name('leave.store');
    Route::delete('/cuti/{leave}', [EmployeeLeaveController::class, 'cancel'])->name('leave.cancel');

    Route::middleware(['auth', 'role:karyawan'])->group(function () {
        Route::get('/dashboard', fn() => view('employee.dashboard'))->name('dashboard');
        Route::get('/gaji', [SalaryController::class, 'index'])->name('salary');
        Route::get('/riwayat-absensi', [SalaryController::class, 'attendanceHistory'])->name('attendance.history');
        Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.index');
        Route::patch('/delivery/{delivery}/status', [DeliveryController::class, 'updateStatus'])->name('delivery.update-status');
        Route::get('/barcode', function () {
            $profile = auth()->user()->employeeProfile;
            return view('employee.barcode', compact('profile'));
        })->name('barcode');
    });
});

// ============ AUTH: STAFF (Admin/Bos/IT) ============
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/login', [StaffAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [StaffAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout');
});

// ============ ABSENSI (Public - Scan QR Statis + Pilih Nama + Wajah) ============
Route::prefix('absensi')->name('attendance.')->group(function () {
    Route::get('/scan', [AttendanceController::class, 'scanPage'])->name('scan');
    Route::get('/pilih-karyawan', [AttendanceController::class, 'selectEmployee'])->name('select-employee');
    Route::get('/wajah/{employeeCode}', [AttendanceController::class, 'facePage'])->name('face');
    Route::post('/proses', [AttendanceController::class, 'process'])->name('process');
});

// ============ ADMIN ============
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Data karyawan (gaji saja, verifikasi murni di IT)
    Route::get('/karyawan', [EmployeeVerificationController::class, 'index'])->name('employees.index');
    Route::post('/karyawan/{profile}/salary', [EmployeeVerificationController::class, 'updateSalary'])->name('employees.salary');

    // Manajemen order
    Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/confirm-payment', [OrderManagementController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/assign-delivery', [OrderManagementController::class, 'assignDelivery'])->name('orders.assign-delivery');

    // Manajemen produk
    Route::resource('products', AdminProductController::class);
    Route::post('/products/{product}/toggle', [AdminProductController::class, 'toggleAvailable'])->name('products.toggle');

    // Manajemen Gaji
    Route::get('/gaji', [SalaryManagementController::class, 'index'])->name('salary.index');
    Route::post('/gaji/generate', [SalaryManagementController::class, 'generateAll'])->name('salary.generate');
    Route::post('/gaji/{salary}/bonus', [SalaryManagementController::class, 'updateBonus'])->name('salary.bonus');
    Route::post('/gaji/submit-bos', [SalaryManagementController::class, 'submitToBos'])->name('salary.submit');

    // Manajemen Cuti Karyawan
    Route::get('/cuti', [AdminLeaveController::class, 'index'])->name('leave.index');
    Route::post('/cuti/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/cuti/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leave.reject');
});

// ============ BOS ============
Route::middleware(['auth', 'role:bos'])->prefix('bos')->name('bos.')->group(function () {
    Route::get('/dashboard', [BosDashboard::class, 'index'])->name('dashboard');
    Route::get('/laporan', [ReportController::class, 'index'])->name('report');
    Route::get('/laporan/pdf', [ReportController::class, 'downloadPdf'])->name('report.pdf');
    Route::get('/laporan/excel', [ReportController::class, 'downloadExcel'])->name('report.excel');
    
    // Approval Gaji
    Route::get('/gaji', [SalaryApprovalController::class, 'index'])->name('salary.index');
    Route::post('/gaji/approve-all', [SalaryApprovalController::class, 'approveAll'])->name('salary.approve-all');
    Route::post('/gaji/{salary}/approve', [SalaryApprovalController::class, 'approveSingle'])->name('salary.approve');
    Route::get('/gaji/{salary}/slip', [SalaryApprovalController::class, 'downloadSlipPdf'])->name('salary.slip');
});

// ============ IT ============
Route::middleware(['auth', 'role:it'])->prefix('it')->name('it.')->group(function () {
    Route::get('/dashboard', [UserManagementController::class, 'index'])->name('dashboard');
    Route::post('/users/{user}/toggle', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
    Route::post('/employees/{profile}/verify', [UserManagementController::class, 'verifyEmployee'])->name('employees.verify');
    Route::post('/employees/{profile}/reject', [UserManagementController::class, 'rejectEmployee'])->name('employees.reject');

    // Absen Manual — route spesifik HARUS di atas route dengan parameter dinamis
    Route::get('/absensi', [AttendanceManagementController::class, 'index'])->name('attendance.index');
    Route::get('/absensi/tambah', [AttendanceManagementController::class, 'create'])->name('attendance.create');
    Route::post('/absensi', [AttendanceManagementController::class, 'store'])->name('attendance.store');
    Route::get('/absensi/{attendance}/edit', [AttendanceManagementController::class, 'edit'])->name('attendance.edit');
    Route::put('/absensi/{attendance}', [AttendanceManagementController::class, 'update'])->name('attendance.update');
    Route::delete('/absensi/{attendance}', [AttendanceManagementController::class, 'destroy'])->name('attendance.destroy');
});

// ============ NOTIFIKASI (semua role yang login) ============
Route::middleware(['auth'])->group(function () {
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifikasi/count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
});