<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware("web")
                ->prefix("dashBoard")
                ->group(base_path("routes/admin.php"));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'is.admin' => \App\Http\Middleware\DashBoard\Admin\IsAdmin::class,
            'role' => \App\Http\Middleware\DashBoard\User\CheckRole::class,
            'phone_verified' => \App\Http\Middleware\DashBoard\User\EnsurePhoneIsVerified::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'https://scrapy.sell-io.app/api/payments/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
