<?php

use App\Http\Controllers\AccountDeletionController;
use App\Http\Controllers\AccountDeletionRequestController;
use App\Http\Controllers\AppStoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Google Play requirements for the parent app
|--------------------------------------------------------------------------
| Public: Digital Asset Links, privacy policy, account-deletion request page.
| Admin:  review/process account-deletion requests.
*/

Route::get('/.well-known/assetlinks.json', [AppStoreController::class, 'assetLinks'])->name('assetlinks');
Route::get('/privacy-policy', [AppStoreController::class, 'privacyPolicy'])->name('privacy-policy');

Route::get('/account/delete', [AccountDeletionController::class, 'show'])->name('account-deletion.show');
Route::post('/account/delete', [AccountDeletionController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('account-deletion.store');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/account-deletion-requests')
    ->name('admin.account-deletion-requests.')
    ->group(function () {
        Route::get('/', [AccountDeletionRequestController::class, 'index'])->name('index');
        Route::patch('/{accountDeletionRequest}/complete', [AccountDeletionRequestController::class, 'complete'])
            ->name('complete')->whereNumber('accountDeletionRequest');
        Route::patch('/{accountDeletionRequest}/reject', [AccountDeletionRequestController::class, 'reject'])
            ->name('reject')->whereNumber('accountDeletionRequest');
    });
