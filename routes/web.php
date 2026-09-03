<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ChildDailyLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Daily Operations (Attendance & Child Daily Logs) - accessible by Admin & Staff
    Route::middleware('role:admin|staff')->group(function () {
        // Attendance Desk
        Route::get('/admin/attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
        Route::post('/admin/attendance', [AttendanceController::class, 'store'])->name('admin.attendance.store');
        Route::put('/admin/attendance/{attendance}', [AttendanceController::class, 'update'])->name('admin.attendance.update');
        Route::delete('/admin/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('admin.attendance.destroy');
        Route::post('/admin/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('admin.attendance.check-in');
        Route::post('/admin/attendance/{attendance}/check-out', [AttendanceController::class, 'checkOut'])->name('admin.attendance.check-out');
        Route::post('/admin/attendance/mark-absent', [AttendanceController::class, 'markAbsent'])->name('admin.attendance.mark-absent');
        Route::post('/admin/attendance/bulk', [AttendanceController::class, 'bulk'])->name('admin.attendance.bulk');

        // Child Daily Logs
        Route::get('/admin/child-daily-logs', [ChildDailyLogController::class, 'index'])->name('admin.child-daily-logs.index');
        Route::get('/admin/child-daily-logs/child/{child}', [ChildDailyLogController::class, 'childDay'])->name('admin.child-daily-logs.child-day');
        Route::post('/admin/child-daily-logs', [ChildDailyLogController::class, 'store'])->name('admin.child-daily-logs.store');
        Route::put('/admin/child-daily-logs/{childDailyLog}', [ChildDailyLogController::class, 'update'])->name('admin.child-daily-logs.update');
        Route::delete('/admin/child-daily-logs/{childDailyLog}', [ChildDailyLogController::class, 'destroy'])->name('admin.child-daily-logs.destroy');
    });

    // Admin-only management routes
    Route::middleware('role:admin')->group(function () {
        // User management
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::patch('/admin/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        // Programs management
        Route::resource('/admin/programs', ProgramController::class, ['as' => 'admin']);

        // Children management
        Route::resource('/admin/children', ChildController::class, ['as' => 'admin']);
        Route::get('/admin/documents/{document}/download', [ChildController::class, 'downloadDocument'])->name('admin.documents.download');
        Route::delete('/admin/documents/{document}', [ChildController::class, 'destroyDocument'])->name('admin.documents.destroy');

        // Enrollments management
        Route::resource('/admin/enrollments', EnrollmentController::class, ['as' => 'admin']);
        Route::patch('/admin/enrollments/{enrollment}/approve', [EnrollmentController::class, 'approve'])->name('admin.enrollments.approve');
        Route::patch('/admin/enrollments/{enrollment}/reject', [EnrollmentController::class, 'reject'])->name('admin.enrollments.reject');
        Route::patch('/admin/enrollments/{enrollment}/withdraw', [EnrollmentController::class, 'withdraw'])->name('admin.enrollments.withdraw');
        Route::patch('/admin/enrollments/{enrollment}/graduate', [EnrollmentController::class, 'graduate'])->name('admin.enrollments.graduate');

        // Role-Permission management
        Route::get('/admin/role-permissions', [RolePermissionController::class, 'index'])->name('admin.role-permissions.index');
        Route::post('/admin/role-permissions', [RolePermissionController::class, 'store'])->name('admin.role-permissions.store');
        Route::put('/admin/role-permissions/{role}', [RolePermissionController::class, 'update'])->name('admin.role-permissions.update');
        Route::delete('/admin/role-permissions/{role}', [RolePermissionController::class, 'destroy'])->name('admin.role-permissions.destroy');
    });
});

