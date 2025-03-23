<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::controller(Admin\AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/clear', 'clearCookies');

    Route::post('/refresh', 'refresh');

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/me', 'me');

    });
});


Route::middleware('auth:api')->group(function () {
    Route::controller(Admin\ProductController::class)->group(function () {
        Route::get('/products', 'index');
        Route::get('/products/{product}', 'show');
        Route::post('/products', 'store');
        Route::post('/products/{product}', 'update');
        Route::delete('/products/{product}', 'destroy');
    });

    Route::controller(Admin\CategoryController::class)->group(function () {
        Route::get('/categories_list', 'list');
    });
});