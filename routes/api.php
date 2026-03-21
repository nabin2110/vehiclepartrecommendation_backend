<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/csrf-cookie', function () {
    $csrfToken = getCsrfToken();
    return response()
        ->json(['message' => 'CSRF cookie issued.'])
        ->withCookie(
            cookie(
                'XSRF-TOKEN',
                $csrfToken,
                60,
                '/',
                null,
                config('app.env') !== 'local',
                false,
                false,
                'Strict'
            )
        );
});

Route::prefix('auth')->group(function () {
    Route::post('initiate-login', [AuthController::class, 'initiateLogin']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
});
