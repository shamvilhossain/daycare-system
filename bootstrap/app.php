<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'parent.child' => \App\Http\Middleware\ParentChildAccess::class,
        ]);

        // SSLCommerz posts cross-site to these URLs; they carry no CSRF token.
        $middleware->preventRequestForgery(except: ['payment/sslcommerz/*']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
