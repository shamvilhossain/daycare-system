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
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TherapySessionController;
use App\Http\Controllers\TherapyServiceController;
use App\Http\Controllers\TherapyPackageController;
use App\Http\Controllers\ChildTherapyPackageController;
use App\Http\Controllers\ChildSafetyCardController;
use App\Models\Announcement;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    $announcements = Announcement::whereIn('audience', ['parents', 'all'])
        ->where('expires_at', '>', now())
        ->where(function ($query) {
            $query->whereNull('published_at')->orWhere('published_at', '<=', now());
        })
        ->orderByRaw('CASE WHEN published_at IS NULL THEN created_at ELSE published_at END DESC')
        ->get();

    return view('welcome', compact('announcements'));
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Public Child Safety QR Card & Found Reporting (Throttled)
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/s/{token}', [ChildSafetyCardController::class, 'show'])->name('safety.card');
    Route::post('/s/{token}/found', [ChildSafetyCardController::class, 'reportFound'])->name('safety.report');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Attendance Desk
    Route::middleware('role_or_permission:admin|attendance.view-any|attendance.view')->group(function () {
        Route::get('/admin/attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
        Route::post('/admin/attendance', [AttendanceController::class, 'store'])->name('admin.attendance.store');
        Route::put('/admin/attendance/{attendance}', [AttendanceController::class, 'update'])->name('admin.attendance.update');
        Route::delete('/admin/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('admin.attendance.destroy');
        Route::post('/admin/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('admin.attendance.check-in');
        Route::post('/admin/attendance/{attendance}/check-out', [AttendanceController::class, 'checkOut'])->name('admin.attendance.check-out');
        Route::post('/admin/attendance/mark-absent', [AttendanceController::class, 'markAbsent'])->name('admin.attendance.mark-absent');
        Route::post('/admin/attendance/bulk', [AttendanceController::class, 'bulk'])->name('admin.attendance.bulk');
    });

    // Child Daily Logs
    Route::middleware('role_or_permission:admin|child-daily-logs.view-any|child-daily-logs.view')->group(function () {
        Route::get('/admin/child-daily-logs', [ChildDailyLogController::class, 'index'])->name('admin.child-daily-logs.index');
        Route::get('/admin/child-daily-logs/child/{child}', [ChildDailyLogController::class, 'childDay'])->name('admin.child-daily-logs.child-day');
        Route::post('/admin/child-daily-logs', [ChildDailyLogController::class, 'store'])->name('admin.child-daily-logs.store');
        Route::put('/admin/child-daily-logs/{childDailyLog}', [ChildDailyLogController::class, 'update'])->name('admin.child-daily-logs.update');
        Route::delete('/admin/child-daily-logs/{childDailyLog}', [ChildDailyLogController::class, 'destroy'])->name('admin.child-daily-logs.destroy');
    });

    // Activity Occurrences (Scheduling, Sessions, & Observations)
    Route::middleware('role_or_permission:admin|activity-occurrences.view-any')->group(function () {
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
    });

    // Activities catalog index & view
    Route::middleware('role_or_permission:admin|activities.view-any')->group(function () {
        Route::get('/admin/activities', [ActivityController::class, 'index'])->name('admin.activities.index');
        Route::get('/admin/activities/{activity}', [ActivityController::class, 'show'])->name('admin.activities.show')->whereNumber('activity');
    });

    // Admin-only management routes (Users & Accounts, Role Permissions, Activity Catalog CRUD)
    Route::middleware('role:admin')->group(function () {
        // User management
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::patch('/admin/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        // Role-Permission management
        Route::get('/admin/role-permissions', [RolePermissionController::class, 'index'])->name('admin.role-permissions.index');
        Route::post('/admin/role-permissions', [RolePermissionController::class, 'store'])->name('admin.role-permissions.store');
        Route::put('/admin/role-permissions/{role}', [RolePermissionController::class, 'update'])->name('admin.role-permissions.update');
        Route::delete('/admin/role-permissions/{role}', [RolePermissionController::class, 'destroy'])->name('admin.role-permissions.destroy');

        // Activity Catalog management (Admin only)
        Route::get('/admin/activities/create', [ActivityController::class, 'create'])->name('admin.activities.create');
        Route::post('/admin/activities', [ActivityController::class, 'store'])->name('admin.activities.store');
        Route::get('/admin/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('admin.activities.edit')->whereNumber('activity');
        Route::put('/admin/activities/{activity}', [ActivityController::class, 'update'])->name('admin.activities.update')->whereNumber('activity');
        Route::patch('/admin/activities/{activity}/toggle-status', [ActivityController::class, 'toggleStatus'])->name('admin.activities.toggle-status')->whereNumber('activity');
        Route::delete('/admin/activities/{activity}', [ActivityController::class, 'destroy'])->name('admin.activities.destroy')->whereNumber('activity');
    });

    // Staff management
    Route::middleware('role_or_permission:admin|staff.view-any')->group(function () {
        Route::patch('/admin/staff/{staff}/toggle-status', [StaffController::class, 'toggleStatus'])->name('admin.staff.toggle-status');
        Route::resource('/admin/staff', StaffController::class, ['as' => 'admin']);
    });

    // Programs management
    Route::middleware('role_or_permission:admin|programs.view-any|programs.view')->group(function () {
        Route::resource('/admin/programs', ProgramController::class, ['as' => 'admin']);
    });

    // Children management
    Route::middleware('role_or_permission:admin|children.view-any|children.view')->group(function () {
        Route::resource('/admin/children', ChildController::class, ['as' => 'admin']);
        Route::post('/admin/children/{child}/safety-tag', [ChildController::class, 'generateSafetyTag'])->name('admin.children.generate-safety-tag');
        Route::patch('/admin/children/safety-tag/{tag}/deactivate', [ChildController::class, 'deactivateSafetyTag'])->name('admin.children.deactivate-safety-tag');
        Route::get('/admin/documents/{document}/download', [ChildController::class, 'downloadDocument'])->name('admin.documents.download');
        Route::delete('/admin/documents/{document}', [ChildController::class, 'destroyDocument'])->name('admin.documents.destroy');
    });

    // Enrollments management
    Route::middleware('role_or_permission:admin|enrollments.view-any|enrollments.view')->group(function () {
        Route::resource('/admin/enrollments', EnrollmentController::class, ['as' => 'admin']);
        Route::patch('/admin/enrollments/{enrollment}/approve', [EnrollmentController::class, 'approve'])->name('admin.enrollments.approve');
        Route::patch('/admin/enrollments/{enrollment}/reject', [EnrollmentController::class, 'reject'])->name('admin.enrollments.reject');
        Route::patch('/admin/enrollments/{enrollment}/withdraw', [EnrollmentController::class, 'withdraw'])->name('admin.enrollments.withdraw');
        Route::patch('/admin/enrollments/{enrollment}/graduate', [EnrollmentController::class, 'graduate'])->name('admin.enrollments.graduate');
    });

    // Invoice & Payment management
    Route::middleware('role_or_permission:admin|invoices.view-any|invoices.view')->group(function () {
        Route::get('/admin/invoices/get-children', [InvoiceController::class, 'getChildrenByParent'])->name('admin.invoices.get-children');
        Route::get('/admin/invoices/get-therapy-sessions', [InvoiceController::class, 'getBillableTherapySessions'])->name('admin.invoices.get-therapy-sessions');
        Route::resource('/admin/invoices', InvoiceController::class, ['as' => 'admin'])->except(['edit', 'update']);
        Route::post('/admin/invoices/{invoice}/payments', [InvoiceController::class, 'addPayment'])->name('admin.invoices.add-payment');
        Route::patch('/admin/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('admin.invoices.cancel');
    });

    // Announcement management
    Route::middleware('role_or_permission:admin|announcements.view-any')->group(function () {
        Route::resource('/admin/announcements', AnnouncementController::class, ['as' => 'admin']);
    });

    // Therapy Sessions management
    Route::middleware('role_or_permission:admin|therapy-sessions.view-any|therapy-sessions.view')->group(function () {
        Route::resource('/admin/therapy-sessions', TherapySessionController::class, ['as' => 'admin'])
            ->parameters(['therapy-sessions' => 'therapySession']);
        Route::patch('/admin/therapy-sessions/{therapySession}/update-status', [TherapySessionController::class, 'updateStatus'])
            ->name('admin.therapy-sessions.update-status');
    });

    // Therapy Services catalog (admin-only CRUD)
    Route::middleware('role:admin')->group(function () {
        Route::resource('/admin/therapy-services', TherapyServiceController::class, ['as' => 'admin'])
            ->parameters(['therapy-services' => 'therapyService']);
    });

    // Therapy Packages catalog (admin-only CRUD)
    Route::middleware('role:admin')->group(function () {
        Route::resource('/admin/therapy-packages', TherapyPackageController::class, ['as' => 'admin'])
            ->parameters(['therapy-packages' => 'therapyPackage'])
            ->except(['show']);
    });

    // Child Therapy Packages (viewing: all roles; purchase/cancel: admin & staff)
    Route::middleware('role_or_permission:admin|child-therapy-packages.view-any|child-therapy-packages.view')->group(function () {
        Route::get('/admin/child-therapy-packages', [ChildTherapyPackageController::class, 'index'])->name('admin.child-therapy-packages.index');
        Route::get('/admin/child-therapy-packages/{childTherapyPackage}', [ChildTherapyPackageController::class, 'show'])->name('admin.child-therapy-packages.show');
        Route::get('/admin/child-therapy-packages-create', [ChildTherapyPackageController::class, 'create'])->name('admin.child-therapy-packages.create');
        Route::post('/admin/child-therapy-packages', [ChildTherapyPackageController::class, 'store'])->name('admin.child-therapy-packages.store');
        Route::patch('/admin/child-therapy-packages/{childTherapyPackage}/cancel', [ChildTherapyPackageController::class, 'cancel'])->name('admin.child-therapy-packages.cancel');
    });

    // Reports
    Route::middleware('role_or_permission:admin|reports.view|reports.billing')->group(function () {
        Route::get('/admin/reports/billing-revenue', [ReportController::class, 'billingRevenue'])->name('admin.reports.billing-revenue');
        Route::get('/admin/reports/activity-calendar', [ReportController::class, 'activityCalendar'])->name('admin.reports.activity-calendar');
    });
});

// Serve files from storage/app/public when public/storage symlink is not created (e.g. Windows/Laragon dev)
Route::get('/storage/{path}', function (string $path) {
    $cleanPath = str_replace(['..', "\0"], '', $path);
    if (Storage::disk('public')->exists($cleanPath)) {
        return Storage::disk('public')->response($cleanPath);
    }
    abort(404);
})->where('path', '.*')->name('storage.local');
