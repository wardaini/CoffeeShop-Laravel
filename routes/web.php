<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AttendanceController;

use App\Http\Controllers\Auth\EmployeeAuthController;
use App\Http\Controllers\Auth\StaffAuthController;

use App\Http\Controllers\Employee\SalaryController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\EmployeeVerificationController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SalaryManagementController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;

use App\Http\Controllers\Bos\DashboardController as BosDashboard;
use App\Http\Controllers\Bos\ReportController;
use App\Http\Controllers\Bos\SalaryApprovalController;

use App\Http\Controllers\IT\UserManagementController;
use App\Http\Controllers\IT\UserEditController;
use App\Http\Controllers\IT\AttendanceManagementController;
use App\Http\Controllers\IT\ActivityLogController;
use App\Http\Controllers\IT\ExportController;

use App\Http\Controllers\Kasir\DashboardController as KasirDashboard;
use App\Http\Controllers\Kasir\OrderQueueController;

use App\Http\Controllers\Barista\DashboardController as BaristaDashboard;
use App\Http\Controllers\Dapur\DashboardController as DapurDashboard;
use App\Http\Controllers\Kurir\DashboardController as KurirDashboard;
use App\Http\Controllers\Cleaning\DashboardController as CleaningDashboard;

// ============ PUBLIC ============
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{product:slug}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/success', [OrderController::class, 'success'])->name('order.success');
Route::get('/order/track', [OrderController::class, 'track'])->name('order.track');
Route::get('/order/{order}/receipt', [OrderController::class, 'receipt'])->name('order.receipt');

// ============ ABSENSI (Public) ============
Route::prefix('absensi')->name('attendance.')->group(function () {
    Route::get('/scan', [AttendanceController::class, 'scanPage'])->name('scan');
    Route::get('/pilih-karyawan', [AttendanceController::class, 'selectEmployee'])->name('select-employee');
    Route::get('/wajah/{employeeCode}', [AttendanceController::class, 'facePage'])->name('face');
    Route::post('/proses', [AttendanceController::class, 'process'])->name('process');
});

// ============ AUTH: KARYAWAN ============
Route::prefix('karyawan')->name('employee.')->group(function () {
    Route::get('/register', [EmployeeAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [EmployeeAuthController::class, 'register'])->name('register.store');
    Route::get('/login', [EmployeeAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [EmployeeAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [EmployeeAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'role:karyawan'])->group(function () {
        Route::get('/dashboard', function () {
            $role = auth()->user()->employeeProfile?->position;
            return match(strtolower($role ?? '')) {
                'kasir'            => redirect()->route('kasir.dashboard'),
                'barista'          => redirect()->route('barista.dashboard'),
                'dapur'            => redirect()->route('dapur.dashboard'),
                'kurir'            => redirect()->route('kurir.dashboard'),
                'cleaning service' => redirect()->route('cleaning.dashboard'),
                default            => view('employee.dashboard'),
            };
        })->name('dashboard');

        Route::get('/profil', fn() => view('employee.profile'))->name('profile');
        Route::get('/gaji', [SalaryController::class, 'index'])->name('salary');
        Route::get('/riwayat-absensi', [SalaryController::class, 'attendanceHistory'])->name('attendance.history');
        Route::get('/barcode', function () {
            $profile = auth()->user()->employeeProfile;
            return view('employee.barcode', compact('profile'));
        })->name('barcode');
        Route::get('/cuti', [EmployeeLeaveController::class, 'index'])->name('leave.index');
        Route::get('/cuti/ajukan', [EmployeeLeaveController::class, 'create'])->name('leave.create');
        Route::post('/cuti', [EmployeeLeaveController::class, 'store'])->name('leave.store');
        Route::delete('/cuti/{leave}', [EmployeeLeaveController::class, 'cancel'])->name('leave.cancel');
    });
});

// ============ AUTH: STAFF ============
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/login', [StaffAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [StaffAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout');
});

// ============ NOTIFIKASI ============
Route::middleware(['auth'])->group(function () {
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifikasi/count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
});

// ============ KASIR ============
Route::middleware(['auth', 'role:karyawan'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirDashboard::class, 'index'])->name('dashboard');
    Route::get('/antrian', [OrderQueueController::class, 'index'])->name('queue');
    Route::post('/orders/{order}/confirm-payment', [OrderQueueController::class, 'confirmPayment'])->name('confirm-payment');
    Route::post('/orders/{order}/send-to-kitchen', [OrderQueueController::class, 'sendToKitchen'])->name('send-to-kitchen');
    Route::post('/orders/{order}/assign-courier', [OrderQueueController::class, 'assignCourier'])->name('assign-courier');
    Route::get('/couriers/available', [OrderQueueController::class, 'availableCouriers'])->name('available-couriers');
    Route::post('/orders/{order}/complete', [OrderQueueController::class, 'complete'])->name('complete');
});

// ============ BARISTA ============
Route::middleware(['auth', 'role:karyawan'])->prefix('barista')->name('barista.')->group(function () {
    Route::get('/dashboard', [BaristaDashboard::class, 'index'])->name('dashboard');
    Route::post('/items/{item}/update-status', [BaristaDashboard::class, 'updateStatus'])->name('update-status');
});

// ============ DAPUR ============
Route::middleware(['auth', 'role:karyawan'])->prefix('dapur')->name('dapur.')->group(function () {
    Route::get('/dashboard', [DapurDashboard::class, 'index'])->name('dashboard');
    Route::post('/items/{item}/update-status', [DapurDashboard::class, 'updateStatus'])->name('update-status');
});

// ============ KURIR ============
Route::middleware(['auth', 'role:karyawan'])->prefix('kurir')->name('kurir.')->group(function () {
    Route::get('/dashboard', [KurirDashboard::class, 'index'])->name('dashboard');
    Route::post('/deliveries/{delivery}/update', [KurirDashboard::class, 'updateStatus'])->name('update-status');
    Route::post('/status', [KurirDashboard::class, 'updateCourierStatus'])->name('update-courier-status');
});

// ============ CLEANING ============
Route::middleware(['auth', 'role:karyawan'])->prefix('cleaning')->name('cleaning.')->group(function () {
    Route::get('/dashboard', [CleaningDashboard::class, 'index'])->name('dashboard');
    Route::post('/schedules/{schedule}/done', [CleaningDashboard::class, 'markDone'])->name('mark-done');
});

// ============ ADMIN ============
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::get('/karyawan', [EmployeeVerificationController::class, 'index'])->name('employees.index');
    Route::post('/karyawan/{profile}/salary', [EmployeeVerificationController::class, 'updateSalary'])->name('employees.salary');

    Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/confirm-payment', [OrderManagementController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/assign-delivery', [OrderManagementController::class, 'assignDelivery'])->name('orders.assign-delivery');

    Route::resource('products', AdminProductController::class);
    Route::post('/products/{product}/toggle', [AdminProductController::class, 'toggleAvailable'])->name('products.toggle');

    Route::get('/gaji', [SalaryManagementController::class, 'index'])->name('salary.index');
    Route::post('/gaji/generate', [SalaryManagementController::class, 'generateAll'])->name('salary.generate');
    Route::post('/gaji/{salary}/bonus', [SalaryManagementController::class, 'updateBonus'])->name('salary.bonus');
    Route::post('/gaji/submit-bos', [SalaryManagementController::class, 'submitToBos'])->name('salary.submit');

    Route::get('/cuti', [AdminLeaveController::class, 'index'])->name('leave.index');
    Route::post('/cuti/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/cuti/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leave.reject');

    Route::get('/cleaning', [\App\Http\Controllers\Admin\CleaningManagementController::class, 'index'])->name('cleaning.index');
    Route::post('/cleaning', [\App\Http\Controllers\Admin\CleaningManagementController::class, 'store'])->name('cleaning.store');
    Route::delete('/cleaning/{schedule}', [\App\Http\Controllers\Admin\CleaningManagementController::class, 'destroy'])->name('cleaning.destroy');
});

// ============ BOS ============
Route::middleware(['auth', 'role:bos'])->prefix('bos')->name('bos.')->group(function () {
    Route::get('/dashboard', [BosDashboard::class, 'index'])->name('dashboard');
    Route::get('/laporan', [ReportController::class, 'index'])->name('report');
    Route::get('/laporan/pdf', [ReportController::class, 'downloadPdf'])->name('report.pdf');
    Route::get('/laporan/excel', [ReportController::class, 'downloadExcel'])->name('report.excel');

    Route::get('/gaji', [SalaryApprovalController::class, 'index'])->name('salary.index');
    Route::post('/gaji/approve-all', [SalaryApprovalController::class, 'approveAll'])->name('salary.approve-all');
    Route::post('/gaji/{salary}/approve', [SalaryApprovalController::class, 'approveSingle'])->name('salary.approve');
    Route::get('/gaji/{salary}/slip', [SalaryApprovalController::class, 'downloadSlipPdf'])->name('salary.slip');
    Route::post('/gaji/generate-staff', [SalaryApprovalController::class, 'generateStaff'])->name('salary.generate-staff');
    Route::post('/gaji/{salary}/staff-bonus', [SalaryApprovalController::class, 'updateStaffBonus'])->name('salary.staff-bonus');
});

// ============ IT ============
Route::middleware(['auth', 'role:it'])->prefix('it')->name('it.')->group(function () {
    Route::get('/dashboard', [UserManagementController::class, 'index'])->name('dashboard');
    Route::post('/users/{user}/toggle', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
    Route::post('/employees/{profile}/verify', [UserManagementController::class, 'verifyEmployee'])->name('employees.verify');
    Route::post('/employees/{profile}/reject', [UserManagementController::class, 'rejectEmployee'])->name('employees.reject');

    // Edit & Hapus User
    Route::get('/users/create', [UserEditController::class, 'create'])->name('users.create');
    Route::post('/users', [UserEditController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserEditController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserEditController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserEditController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/reset-password', [UserEditController::class, 'resetPassword'])->name('users.reset-password');

    // Absensi Manual
    Route::get('/absensi', [AttendanceManagementController::class, 'index'])->name('attendance.index');
    Route::get('/absensi/tambah', [AttendanceManagementController::class, 'create'])->name('attendance.create');
    Route::post('/absensi', [AttendanceManagementController::class, 'store'])->name('attendance.store');
    Route::get('/absensi/{attendance}/edit', [AttendanceManagementController::class, 'edit'])->name('attendance.edit');
    Route::put('/absensi/{attendance}', [AttendanceManagementController::class, 'update'])->name('attendance.update');
    Route::delete('/absensi/{attendance}', [AttendanceManagementController::class, 'destroy'])->name('attendance.destroy');

    // Log Aktivitas
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs');

    // Export Data
    Route::get('/export', [ExportController::class, 'index'])->name('export');
    Route::get('/export/users', [ExportController::class, 'exportUsers'])->name('export.users');
    Route::get('/export/orders', [ExportController::class, 'exportOrders'])->name('export.orders');
    Route::get('/export/attendances', [ExportController::class, 'exportAttendances'])->name('export.attendances');
    Route::get('/export/salaries', [ExportController::class, 'exportSalaries'])->name('export.salaries');
});