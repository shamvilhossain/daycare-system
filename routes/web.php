<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php — add inside the 'guest' middleware group
Route::get('/forgot-password', [PasswordResetController::class,'request'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class,'email'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class,'reset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class,'update'])->name('password.update');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    //Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::resource('programs', ProgramController::class);
// });

// Route::middleware(['auth', 'role:admin|staff'])->group(function () {
//     Route::resource('children', ChildController::class);
// });
