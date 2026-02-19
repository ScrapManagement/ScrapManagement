<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;



Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [UserController::class, 'store']);
});

Route::prefix('authAdmin')->group(function () {

    Route::post('login',    [AdminAuthController::class, 'login']);
});

Route::prefix('admin')
    ->middleware('auth:admin-api')
    ->group(function () {
        // Auth Actions
        Route::get('me',            [AdminAuthController::class, 'me']);
        Route::post('logout',       [AdminAuthController::class, 'logout']);
        Route::post('refresh',      [AdminAuthController::class, 'refresh']);

        // CRUD Admin
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


Route::prefix('user')
    ->middleware('auth:api')
    ->group(function () {

        // Auth Actions
        Route::get('me',        [AuthController::class, 'me']);
        Route::post('logout',   [AuthController::class, 'logout']);
        Route::post('refresh',  [AuthController::class, 'refresh']);

        // CRUD Users
        Route::get('/',                 [UserController::class, 'index']);
        Route::post('/',                [UserController::class, 'store']);
        Route::get('trashed',           [UserController::class, 'trashed']);

        Route::get('{id}',              [UserController::class, 'show']);
        Route::get('/profile',              [UserController::class, 'profile']);
        Route::post('{id}',              [UserController::class, 'update']);

        Route::delete('{id}',           [UserController::class, 'softDelete']);
        Route::delete('{id}/force',     [UserController::class, 'forceDelete']);
        Route::post('{id}/restore',     [UserController::class, 'restore']);
    });
