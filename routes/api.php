<?php

use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\Users\UsersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UsersController::class, 'show']);
    Route::put('/user', [UsersController::class, 'update']);
    Route::put('/user/password', [UsersController::class, 'updatePassword']);
});

Route::get('/order/{number}', [OrdersController::class, 'get']);

require __DIR__.'/auth.php';
