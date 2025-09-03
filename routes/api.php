<?php

use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\Users\EditUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::put('/user', [EditUserController::class, 'update']);
    Route::put('/user/password', [EditUserController::class, 'updatePassword']);
});

Route::get('/order/{number}', [OrdersController::class, 'get']);

require __DIR__.'/auth.php';
