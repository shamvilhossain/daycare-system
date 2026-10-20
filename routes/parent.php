<?php

use App\Http\Controllers\Parent\ChildSwitchController;
use App\Http\Controllers\Parent\FeeController;
use App\Http\Controllers\Parent\MoreController;
use App\Http\Controllers\Parent\NoticeController;
use App\Http\Controllers\Parent\PhotoController;
use App\Http\Controllers\Parent\ProfileController;
use App\Http\Controllers\Parent\TherapyController;
use App\Http\Controllers\Parent\TodayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Parent Portal (mobile-first)
|--------------------------------------------------------------------------
| Separate from the AdminLTE /admin/* portal. Every controller here starts
| its queries from the logged-in parent's own children (see ParentChildAccess).
*/

Route::middleware(['auth', 'role:parent', 'parent.child'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::redirect('/', '/parent/today')->name('home');

        Route::post('/children/{child}/switch', ChildSwitchController::class)
            ->name('children.switch')->whereNumber('child');

        Route::get('/today', TodayController::class)->name('today');
        Route::get('/photos', PhotoController::class)->name('photos');
        Route::get('/therapy', TherapyController::class)->name('therapy');

        Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
        Route::get('/fees/{invoice}', [FeeController::class, 'show'])->name('fees.show')->whereNumber('invoice');
        Route::get('/fees/{invoice}/pay', [FeeController::class, 'pay'])->name('fees.pay')->whereNumber('invoice');
        Route::post('/fees/{invoice}/pay', [FeeController::class, 'startPayment'])
            ->name('fees.pay.start')->whereNumber('invoice')->middleware('throttle:10,1');

        Route::get('/notices', NoticeController::class)->name('notices');
        Route::get('/profile', ProfileController::class)->name('profile');
        Route::get('/more', MoreController::class)->name('more');
    });
