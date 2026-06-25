<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
        // Custom middleware aliases
        'superadmin' => \App\Http\Middleware\SuperAdmin::class,
        'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
    ]);

    $middleware->appendToGroup('web', \App\Http\Middleware\CheckUserStatus::class);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($request->is('admin') || $request->is('admin/*') || in_array('admin', $e->guards(), true)) {
                return redirect()
                    ->guest(route('admin.login'))
                    ->with('status', 'Your admin session expired. Please sign in again to continue.');
            }

            return null;
        });

        $exceptions->render(function (InvalidSignatureException $e, $request) {
            if ($request->routeIs('verification.verify')) {
                return redirect()->route('verification.notice')
                    ->with('warning', 'This verification link has expired. Please request a new one below.');
            }

            return null;
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->routeIs('verification.verify')) {
                return redirect()->route('verification.notice')
                    ->with('warning', 'This verification link is no longer valid for your current session. Please resend a new link.');
            }

            return null;
        });

        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()
                    ->guest(route('admin.login'))
                    ->with('status', 'Your admin session expired. Please sign in again to continue.');
            }

            return redirect()
                ->guest(route('login'))
                ->with('status', 'Your session expired. Please sign in again to continue.');
        });

        $exceptions->respond(function (Response $response, $exception, $request) {
            if ($response->getStatusCode() !== 419 || $request->expectsJson()) {
                return $response;
            }

            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()
                    ->guest(route('admin.login'))
                    ->with('status', 'Your admin session expired. Please sign in again to continue.');
            }

            return redirect()
                ->guest(route('login'))
                ->with('status', 'Your session expired. Please sign in again to continue.');
        });
    })->create();
