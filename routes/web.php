<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityOccurrenceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ChildDailyLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ReportController;
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

    // Daily Operations (Attendance, Child Daily Logs, Activity Sessions) - accessible by Admin & Staff
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

        // Activity Occurrences (Scheduling, Sessions, & Observations)
        Route::get('/admin/activity-occurrences', [ActivityOccurrenceController::class, 'index'])->name('admin.activity-occurrences.index');
        Route::get('/admin/activity-occurrences/create', [ActivityOccurrenceController::class, 'create'])->name('admin.activity-occurrences.create');
        Route::post('/admin/activity-occurrences', [ActivityOccurrenceController::class, 'store'])->name('admin.activity-occurrences.store');
        Route::get('/admin/activity-occurrences/{activityOccurrence}', [ActivityOccurrenceController::class, 'show'])->name('admin.activity-occurrences.show')->whereNumber('activityOccurrence');
        Route::get('/admin/activity-occurrences/{activityOccurrence}/edit', [ActivityOccurrenceController::class, 'edit'])->name('admin.activity-occurrences.edit')->whereNumber('activityOccurrence');
        Route::put('/admin/activity-occurrences/{activityOccurrence}', [ActivityOccurrenceController::class, 'update'])->name('admin.activity-occurrences.update')->whereNumber('activityOccurrence');
        Route::patch('/admin/activity-occurrences/{activityOccurrence}/status', [ActivityOccurrenceController::class, 'updateStatus'])->name('admin.activity-occurrences.update-status')->whereNumber('activityOccurrence');
        Route::post('/admin/activity-occurrences/{activityOccurrence}/media', [ActivityOccurrenceController::class, 'uploadMedia'])->name('admin.activity-occurrences.upload-media')->whereNumber('activityOccurrence');
        Route::delete('/admin/activity-occurrences/media/{activityMedia}', [ActivityOccurrenceController::class, 'destroyMedia'])->name('admin.activity-occurrences.destroy-media')->whereNumber('activityMedia');
        Route::delete('/admin/activity-occurrences/{activityOccurrence}', [ActivityOccurrenceController::class, 'destroy'])->name('admin.activity-occurrences.destroy')->whereNumber('activityOccurrence');

        // Activities catalog index & view
        Route::get('/admin/activities', [ActivityController::class, 'index'])->name('admin.activities.index');
        Route::get('/admin/activities/{activity}', [ActivityController::class, 'show'])->name('admin.activities.show')->whereNumber('activity');
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

        // Activity Catalog management (Admin only)
        Route::get('/admin/activities/create', [ActivityController::class, 'create'])->name('admin.activities.create');
        Route::post('/admin/activities', [ActivityController::class, 'store'])->name('admin.activities.store');
        Route::get('/admin/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('admin.activities.edit')->whereNumber('activity');
        Route::put('/admin/activities/{activity}', [ActivityController::class, 'update'])->name('admin.activities.update')->whereNumber('activity');
        Route::patch('/admin/activities/{activity}/toggle-status', [ActivityController::class, 'toggleStatus'])->name('admin.activities.toggle-status')->whereNumber('activity');
        Route::delete('/admin/activities/{activity}', [ActivityController::class, 'destroy'])->name('admin.activities.destroy')->whereNumber('activity');

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

        // Reports
        Route::get('/admin/reports/activity-calendar', [ReportController::class, 'activityCalendar'])->name('admin.reports.activity-calendar');
    });
});
