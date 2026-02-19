<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminController;
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

Route::prefix('admin')
    ->middleware('auth:admin-api')
    ->group(function () {
        Route::get('/',        [AdminController::class, 'index']);
        Route::post('/',       [AdminController::class, 'store']);
        Route::get('/profile',      [AdminController::class, 'profile']);
        Route::get('{id}',     [AdminController::class, 'show']);
        Route::post('{id}',     [AdminController::class, 'update']);
        Route::delete('{id}',  [AdminController::class, 'softDelete']);
        Route::get('/trashed', [AdminController::class, 'trashed']);
        Route::delete('{id}/force',  [AdminController::class, 'forceDelete']);
        Route::post('{id}/restore', [AdminController::class, 'restore']);

    });
