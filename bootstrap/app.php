<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))

    /*-------------------------------------------------------------
     |  Rutas (sin cambios)
     |------------------------------------------------------------*/
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        api:      __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health:   '/up',
    )

    /*-------------------------------------------------------------
     |  Middleware
     |------------------------------------------------------------*/
    ->withMiddleware(function (Middleware $middleware): void {

        /* 🔸 ALIAS NATIVOS */
        $middleware->alias([
            'auth'     => Illuminate\Auth\Middleware\Authenticate::class,
            'guest'    => Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'verified' => Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);

        /* 🔸 ALIAS DE SPATIE PERMISSION */
        $middleware->alias([
            'role'               => RoleMiddleware::class,
            'permission'         => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

         /* 🔸 ALIAS DE API-KEY */
    $middleware->alias([
        'check-api-key' => App\Http\Middleware\CheckApiKey::class,
    ]);


        /* 🔸 GRUPO WEB: sesiones, cookies y CSRF */
        $middleware->web([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
        ]);
    })

    /*-------------------------------------------------------------
     |  Excepciones (sin cambios)
     |------------------------------------------------------------*/
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    ->create();
