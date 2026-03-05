<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Payment\PackageController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Payment\WalletController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\Category\CategoryController;
use Illuminate\Support\Facades\Route;



Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [UserController::class, 'store']);
    Route::post('verify-otp', [UserController::class, 'verifyOtp']);
    Route::get('resend-otp', [UserController::class, 'resendOtp']);
});

Route::prefix('authAdmin')->group(function () {

    Route::post('login',    [AdminAuthController::class, 'login']);
});


Route::prefix('admin')
    ->middleware('auth:admin-api')
    ->group(function () {
        
        // 1. Auth Actions & Profile
        Route::get('me',            [AdminAuthController::class, 'me']);
        Route::post('logout',       [AdminAuthController::class, 'logout']);
        Route::post('refresh',      [AdminAuthController::class, 'refresh']);
        Route::get('/profile',      [AdminController::class, 'profile']);
        Route::get('/trashed',      [AdminController::class, 'trashed']);

        // 2. Categories Management
        Route::get('categories/trashed',    [CategoryController::class, 'trashed']);

        Route::get('categories',        [CategoryController::class, 'index']);
        Route::get('categories/{id}',   [CategoryController::class, 'show']);

        Route::post('categories',       [CategoryController::class, 'store']);
        Route::post('categories/{id}',  [CategoryController::class, 'update']);
        Route::delete('categories/{id}',[CategoryController::class, 'softDelete']);

        Route::post('categories/{id}/restore', [CategoryController::class, 'restore']);
        Route::delete('categories/{id}/force', [CategoryController::class, 'forceDelete']);

        // 3. Products Management
        Route::post('products/{id}/status',  [ProductController::class, 'changeStatus']);

        // 4. Admins Management
        Route::get('/',        [AdminController::class, 'index']);
        Route::post('/',       [AdminController::class, 'store']);
        
        Route::get('{id}',     [AdminController::class, 'show']);
        Route::post('{id}',    [AdminController::class, 'update']);
        Route::delete('{id}',  [AdminController::class, 'softDelete']);
        Route::delete('{id}/force',  [AdminController::class, 'forceDelete']);
        Route::post('{id}/restore', [AdminController::class, 'restore']);
    });


Route::prefix('user')
    ->middleware('auth:api','phone_verified')
    ->group(function () {
        Route::get('me',        [AuthController::class, 'me']);
        Route::post('logout',   [AuthController::class, 'logout']);
        Route::post('refresh',  [AuthController::class, 'refresh']);

        Route::get('/',                 [UserController::class, 'index']);
        Route::post('/',                [UserController::class, 'store']);
        Route::get('trashed',           [UserController::class, 'trashed']);

        Route::get('/profile',          [UserController::class, 'profile']);
        Route::get('{id}',              [UserController::class, 'show']);
        Route::post('{id}',             [UserController::class, 'update']);

        Route::delete('{id}',           [UserController::class, 'softDelete']);
        Route::delete('{id}/force',     [UserController::class, 'forceDelete']);
        Route::post('{id}/restore',     [UserController::class, 'restore']);
    });

Route::prefix('products')
    ->middleware('auth:api','phone_verified')
    ->group(function () {

        Route::get('/',           [ProductController::class, 'index']);
        Route::post('/',          [ProductController::class, 'store']);

        Route::get('/trashed',    [ProductController::class, 'trashed']);

        Route::get('{id}/unlock-cost', [ProductController::class, 'getUnlockCost']);
        Route::post('/{id}/unlock',    [ProductController::class, 'unlock']);

        Route::get('{id}',        [ProductController::class, 'show']);
        Route::post('{id}',       [ProductController::class, 'update']);
        Route::delete('{id}',     [ProductController::class, 'softDelete']);
        Route::delete('{id}/force', [ProductController::class, 'forceDelete']);
        Route::post('{id}/restore', [ProductController::class, 'restore']);
    });


Route::prefix('categories')
    ->middleware('auth:api','phone_verified')
    ->group(function () {
        Route::get('/',    [CategoryController::class, 'index']);
        Route::get('{id}', [CategoryController::class, 'show']);
    });

Route::prefix('wallet')
    ->middleware('auth:api','phone_verified')
    ->group(function () {

        Route::get('/balance', [WalletController::class, 'balance']);
        Route::get('/transactions', [WalletController::class, 'transactions']);
    });

Route::prefix('packages')
    ->middleware('auth:api','phone_verified')
    ->group(function () {
        Route::post('{id}/pay', [PackageController::class, 'pay']);
    });

Route::prefix('packages')
    ->middleware('auth:admin-api')
    ->group(function () {
        Route::get('/', [PackageController::class, 'index']);
        Route::post('/', [PackageController::class, 'store']);
        Route::get('{id}', [PackageController::class, 'show']);
        Route::post('{id}', [PackageController::class, 'update']);
        Route::delete('{id}', [PackageController::class, 'destroy']);
        Route::post('{id}/status', [PackageController::class, 'changeStatus']);
    });


Route::get('/payment/callback', [PaymentController::class, 'callback']);
Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/failed', [PaymentController::class, 'failed'])->name('payment.failed');
