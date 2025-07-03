<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// use App\Http\Middleware\RedirectIfAuthenticated;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))

    /*-------------------------------------------------------------
     |  Rutas (sin cambios)
     |------------------------------------------------------------*/
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health:   '/up',
    )

    /*-------------------------------------------------------------
     |  Middleware
     |------------------------------------------------------------*/
    ->withMiddleware(function (Middleware $middleware): void {

        /* 🔸 ALIAS NATIVOS (ya venían, pon los que uses) */
        $middleware->alias([
            'auth'     => Illuminate\Auth\Middleware\Authenticate::class,
            'guest'    => Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'verified' => Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);

        /* 🔸 ALIAS DE SPATIE PERMISSION (lo importante) */
        $middleware->alias([
            'role'               => Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        /* (Opcional) middleware globales o de grupo:
           $middleware->appendGlobal(\App\Http\Middleware\TrustProxies::class);
           $middleware->web([...]);
           $middleware->api([...]);
        */
    })

    /*-------------------------------------------------------------
     |  Excepciones (sin cambios)
     |------------------------------------------------------------*/
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    ->create();
