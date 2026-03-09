<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API Routes (no authentication required)
Route::prefix('v1')->group(function () {

    // Services API
    Route::get('/services', [ServiceController::class, 'index'])->name('api.services.index');
    Route::get('/services/{id}', [ServiceController::class, 'show'])->name('api.services.show');
    Route::get('/categories', [ServiceController::class, 'categories'])->name('api.categories.index');

    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    })->name('api.health');
});

// Example authenticated routes (if needed later)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user', function (Request $request) {
//         return $request->user();
//     });
// });
