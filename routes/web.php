<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
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

    // Role-Permission management (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/role-permissions', [RolePermissionController::class, 'index'])->name('admin.role-permissions.index');
        Route::post('/admin/role-permissions', [RolePermissionController::class, 'store'])->name('admin.role-permissions.store');
        Route::put('/admin/role-permissions/{role}', [RolePermissionController::class, 'update'])->name('admin.role-permissions.update');
        Route::delete('/admin/role-permissions/{role}', [RolePermissionController::class, 'destroy'])->name('admin.role-permissions.destroy');
    });
});
