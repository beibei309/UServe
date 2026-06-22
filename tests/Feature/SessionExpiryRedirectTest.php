<?php

use Illuminate\Support\Facades\Route;

it('redirects web 419 responses to login instead of showing the page expired screen', function () {
    Route::post('/_test/session-expired', fn () => abort(419))->middleware('web');

    $response = $this->post('/_test/session-expired');

    $response->assertRedirect(route('login', absolute: false));
    $response->assertSessionHas('status', 'Your session expired. Please sign in again to continue.');
});
