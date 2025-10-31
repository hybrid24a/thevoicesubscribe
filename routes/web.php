<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ParamsController;
use App\Http\Controllers\Checkout\CheckoutController;
use App\Http\Controllers\Checkout\PaymentController;
use App\Http\Middleware\ExceptWpCookies;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

$checkoutDomain = parse_url(config('app.checkout_url'), PHP_URL_HOST);

Route::domain($checkoutDomain)
    ->middleware(['web', ExceptWpCookies::class])
    ->withoutMiddleware([EncryptCookies::class])
    ->name('checkout.')
    ->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store']);

        Route::get('/login', [CheckoutController::class, 'checkoutLogin'])->name('login');

        // Optional: catch-all inside checkout.
        Route::fallback(fn() => abort(404));


        Route::get('/pay/{order}', [PaymentController::class, 'preparePayment'])->name('pay');
        Route::prefix('/pay')->name('pay.')->withoutMiddleware([VerifyCsrfToken::class])->group(function () {
            Route::post('/', [PaymentController::class, 'paymentCallback'])->name('callback');
            Route::get('/ok/{number}', [PaymentController::class, 'ok'])->name('ok');
            Route::get('/fail/{number}', [PaymentController::class, 'fail'])->name('fail');
        });
    });

$adminDomain = parse_url(config('app.admin_url'), PHP_URL_HOST);

Route::domain($adminDomain)
    ->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.orders.list');
        });

        Route::middleware('guest.admin')->group(function () {
            Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
            Route::post('/login', [LoginController::class, 'login']);
        });
        Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

        // Protected Admin Routes
        Route::middleware('auth.admin')->group(function () {
            Route::get('/params', [ParamsController::class, 'index'])->name('admin.params');
            Route::post('/params', [ParamsController::class, 'store']);

            Route::get('/orders', [OrdersController::class, 'list'])->name('admin.orders.list');
        });
    });
