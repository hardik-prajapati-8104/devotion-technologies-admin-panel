<?php

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',

        then: function () {
            require base_path('routes/admin.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'ip.allowed'       => \App\Http\Middleware\EnsureIpAllowed::class,
            'session.timeout'  => \App\Http\Middleware\EnsureSessionNotTimedOut::class,
            'password.current' => \App\Http\Middleware\EnsurePasswordIsCurrent::class,
            '2fa.verified'     => \App\Http\Middleware\EnsureTwoFactorVerified::class,
        ]);

        RedirectIfAuthenticated::redirectUsing(function (Request $request) {
            if ($request->is('admin/*') || $request->routeIs('admin.*')) {
                return route('admin.dashboard');
            }

            return route('coming-soon');
        });

        $middleware->redirectGuestsTo(
            fn (Request $request) => route('admin.login')
        );

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

    })
    ->create();