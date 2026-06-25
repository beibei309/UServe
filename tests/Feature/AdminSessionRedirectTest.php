<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;

test('expired admin page requests redirect to admin login', function () {
    $request = request()->create('/admin/dashboard', 'GET');

    $response = app()->make(Illuminate\Contracts\Debug\ExceptionHandler::class)
        ->render($request, new AuthenticationException('Unauthenticated.', ['admin']));

    expect($response->isRedirect(route('admin.login')))->toBeTrue();
});

test('expired admin form requests redirect to admin login', function () {
    $request = request()->create('/admin/services/1/approve', 'POST');

    $response = app()->make(Illuminate\Contracts\Debug\ExceptionHandler::class)
        ->render($request, new TokenMismatchException('CSRF token mismatch.'));

    expect($response->isRedirect(route('admin.login')))->toBeTrue();
});
