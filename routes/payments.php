<?php

use App\Http\Controllers\PaymentLinkController;
use App\Http\Controllers\PublicPaymentController;
use App\Http\Controllers\SslCommerzCallbackController;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/*
|--------------------------------------------------------------------------
| Online payments (SSLCommerz)
|--------------------------------------------------------------------------
*/

// Admin: create a signed payment link for an invoice.
Route::post('/admin/invoices/{invoice}/payment-link', [PaymentLinkController::class, 'store'])
    ->middleware(['auth', 'role_or_permission:admin|invoices.update|payments.create'])
    ->name('admin.invoices.payment-link');

// Public signed payment link — no login. Signature is checked in the controller.
Route::get('/pay/{invoice}', [PublicPaymentController::class, 'show'])->name('pay.show')->whereNumber('invoice');
Route::post('/pay/{invoice}', [PublicPaymentController::class, 'start'])
    ->name('pay.start')->whereNumber('invoice')->middleware('throttle:10,1');

// Receipt / result after returning from the gateway (signed, no login).
Route::get('/payment/receipt/{transaction}', [PublicPaymentController::class, 'receipt'])->name('payment.receipt');

// Gateway callbacks: cross-site POSTs from SSLCommerz.
// No session/cookies/CSRF: never depend on (or overwrite) the parent's login cookie.
Route::prefix('payment/sslcommerz')
    ->name('payment.sslcommerz.')
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class, // Laravel 13's CSRF middleware
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
    ])
    ->group(function () {
        Route::match(['get', 'post'], '/success', [SslCommerzCallbackController::class, 'success'])->name('success');
        Route::match(['get', 'post'], '/fail', [SslCommerzCallbackController::class, 'fail'])->name('fail');
        Route::match(['get', 'post'], '/cancel', [SslCommerzCallbackController::class, 'cancel'])->name('cancel');
        Route::post('/ipn', [SslCommerzCallbackController::class, 'ipn'])->name('ipn');
    });
