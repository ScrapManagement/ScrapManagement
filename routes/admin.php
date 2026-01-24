<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashBoard\Admin\AdminController;
use App\Http\Controllers\DashBoard\Admin\LoginController;

use App\Http\Controllers\DashBoard\Category\CategoryController;




Route::get('loginAdmin', [LoginController::class, 'index'])->name('login.index');
Route::post('loginAdmin/check', [LoginController::class, 'check'])->name('login.check');
Route::get('logoutAdmin', [LoginController::class, 'logout'])->name('login.logout');

Route::middleware(['auth:admin', 'is.admin'])->group(function () {
    Route::get('/', function () {
        return view("dashBoard.layout.main");
    });

    Route::resource("admin", AdminController::class);

    Route::resource("categories", CategoryController::class);
});
