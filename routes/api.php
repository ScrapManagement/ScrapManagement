<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\User\AuthController;
use Illuminate\Support\Facades\Route;



Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

Route::prefix('authAdmin')->group(function () {

    Route::post('login',    [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin-api')->group(function () {
        Route::get('me',            [AdminAuthController::class, 'me']);
        Route::post('logout',       [AdminAuthController::class, 'logout']);
        Route::post('refresh',      [AdminAuthController::class, 'refresh']);
    });

});
