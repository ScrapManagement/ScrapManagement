<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashBoard\User\UserController;
use App\Http\Controllers\DashBoard\Admin\AdminController;
use App\Http\Controllers\DashBoard\Admin\LoginController;
use App\Http\Controllers\DashBoard\Product\ProductController;
use App\Http\Controllers\DashBoard\Category\CategoryController;
use App\Http\Controllers\DashBoard\Order\OrderController;


Route::get('loginAdmin', [LoginController::class, 'index'])->name('login.index');
Route::post('loginAdmin/check', [LoginController::class, 'check'])->name('login.check');
Route::get('logoutAdmin', [LoginController::class, 'logout'])->name('login.logout');

Route::middleware(['auth:admin', 'is.admin'])->group(function () {

    Route::get('/', function () {
        return view("dashBoard.layout.main");
    });

    Route::resource("admin", AdminController::class);
    Route::resource("user", UserController::class);
    Route::resource("categories", CategoryController::class);
    Route::resource("product", ProductController::class);
    Route::patch('/product/{id}/status', [ProductController::class, 'changeStatus'])
    ->name('product.changeStatus');
    Route::resource("order", OrderController::class);
});
