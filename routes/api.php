<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Auction\AuctionController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Payment\PackageController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Payment\WalletController;
use App\Http\Controllers\Api\Product\FavoriteController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;



Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [UserController::class, 'store']);
    Route::post('verify-otp', [UserController::class, 'verifyOtp']);
    Route::get('resend-otp', [UserController::class, 'resendOtp']);
    Route::get('categories',        [CategoryController::class, 'index']);
    Route::get('/packages', [PackageController::class, 'index']);
    Route::get('products/approved',           [ProductController::class, 'approvedProducts']);
    Route::get('products/coins',              [ProductController::class, 'getCoinProducts']);
    Route::get('products/auction',            [ProductController::class, 'getAuctionProducts']);
    Route::get('auctions', [AuctionController::class, 'index']);
    Route::get('auctions/{auction}', [AuctionController::class, 'show']);
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
        Route::delete('categories/{id}', [CategoryController::class, 'softDelete']);
        Route::post('categories/{id}/restore', [CategoryController::class, 'restore']);
        Route::delete('categories/{id}/force', [CategoryController::class, 'forceDelete']);


        //3.Products Management
        Route::get('products',           [ProductController::class, 'index']);
        Route::get('products/{id}',      [ProductController::class, 'show']);
        Route::post('products/{id}/material-priority', [ProductController::class, 'updateMaterialPriority']);
        Route::post('products/{id}/status',  [ProductController::class, 'changeStatus']);
        Route::post('products/{id}/sale-type',  [ProductController::class, 'changeSaleType']);
        Route::delete('products/{id}/force', [ProductController::class, 'forceDelete']);
        Route::post('products/{id}/restore', [ProductController::class, 'restore']);
        Route::get('products/trashed',    [ProductController::class, 'trashed']);


        //4. User Management
        Route::get('user',                 [UserController::class, 'index']);
        Route::delete('user/{id}/force',     [UserController::class, 'forceDelete']);
        Route::post('user/{id}/restore',     [UserController::class, 'restore']);
        Route::get('user/trashed',           [UserController::class, 'trashed']);
        Route::get('user/pending-id-cards', [UserController::class, 'pendingIdCards']);
        Route::get('user/{user}/id-card', [UserController::class, 'showIdCard']);
        Route::post('user/{user}/verify-id-card', [UserController::class, 'verifyIdCard']);

        //5. Package Management
        Route::get('packages', [PackageController::class, 'index']);
        Route::post('packages', [PackageController::class, 'store']);
        Route::get('packages/{id}', [PackageController::class, 'show']);
        Route::post('packages/{id}', [PackageController::class, 'update']);
        Route::delete('packages/{id}', [PackageController::class, 'destroy']);
        Route::post('packages/{id}/status', [PackageController::class, 'changeStatus']);

        //6. Auctions Management
        Route::post('products/{product}/auctions', [AuctionController::class, 'store']);
        Route::post('auctions/{auction}', [AuctionController::class, 'update']);
        Route::delete('auctions/{auction}', [AuctionController::class, 'destroy']);
        Route::post('auctions/{auction}/activate', [AuctionController::class, 'activate']);
        Route::post('auctions/{auction}/end', [AuctionController::class, 'end']);
        Route::post('auctions/{auction}/mark-not-serious', [AuctionController::class, 'markWinnerNotSerious']);

        // 7. Admins Management
        Route::get('/',        [AdminController::class, 'index']);
        Route::post('/',       [AdminController::class, 'store']);
        Route::get('{id}',     [AdminController::class, 'show']);
        Route::post('{id}',    [AdminController::class, 'update']);
        Route::delete('{id}',  [AdminController::class, 'softDelete']);
        Route::delete('{id}/force',  [AdminController::class, 'forceDelete']);
        Route::post('{id}/restore', [AdminController::class, 'restore']);
    });


Route::prefix('user')
    ->middleware('auth:api', 'phone_verified')
    ->group(function () {
        Route::get('me',        [AuthController::class, 'me']);
        Route::post('logout',   [AuthController::class, 'logout']);
        Route::post('refresh',  [AuthController::class, 'refresh']);
        //  Route::get('/',                 [UserController::class, 'index']);
        //  Route::post('/',                [UserController::class, 'store']);
        Route::get('/profile',          [UserController::class, 'profile']);
        Route::post('upload-id-card', [UserController::class, 'uploadIDCard']);
        Route::get('status-id-card', [UserController::class, 'idCardStatus']);
        Route::get('{id}',              [UserController::class, 'show']);
        Route::post('{id}',             [UserController::class, 'update']);
        Route::delete('{id}',           [UserController::class, 'softDelete']);
    });

Route::prefix('products')
    ->middleware('auth:api', 'phone_verified')
    ->group(function () {
        Route::post('/',          [ProductController::class, 'store']);
        Route::get('{id}/unlock-cost', [ProductController::class, 'getUnlockCost']);
        Route::post('/{id}/unlock',    [ProductController::class, 'unlock']);
         Route::get('my-products', [ProductController::class, 'myProducts']);
        Route::get('/my-unlocked-products', [ProductController::class, 'myUnlockedProducts']);
        Route::get('{id}',        [ProductController::class, 'show']);
        Route::post('{id}',       [ProductController::class, 'update']);
        Route::delete('{id}',     [ProductController::class, 'softDelete']);
    });

Route::prefix('categories')
    ->middleware('auth:api', 'phone_verified')
    ->group(function () {
        Route::get('/',    [CategoryController::class, 'index']);
        Route::get('{id}', [CategoryController::class, 'show']);
    });

Route::prefix('wallet')
    ->middleware('auth:api', 'phone_verified')
    ->group(function () {

        Route::get('/balance', [WalletController::class, 'balance']);
        Route::get('/transactions', [WalletController::class, 'transactions']);
    });

Route::prefix('packages')
    ->middleware('auth:api', 'phone_verified')
    ->group(function () {
        Route::post('{id}/pay', [PackageController::class, 'pay']);
    });

Route::prefix('favorites')
    ->middleware('auth:api', 'phone_verified')
    ->group(function () {
        Route::post('/{productId}', [FavoriteController::class, 'add']);
        Route::delete('/{productId}', [FavoriteController::class, 'remove']);
        Route::get('/', [FavoriteController::class, 'index']);
    });

Route::prefix('auctions')
    ->middleware('auth:api', 'phone_verified')
    ->group(function () {
        Route::post('{auction}/pay', [AuctionController::class, 'join']);
        Route::post('{auction}/bid', [AuctionController::class, 'bid']);
        Route::get('{auction}/bids', [AuctionController::class, 'bids']);
    });


Route::get('/payment/callback', [PaymentController::class, 'callback']);
Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/failed', [PaymentController::class, 'failed'])->name('payment.failed');
