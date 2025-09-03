<?php

use App\Http\Controllers\Checkout\CheckoutController;
use App\Http\Controllers\Checkout\CMIController;
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


        Route::get('/checkout/cmi/{order}', [CMIController::class, 'preparePayment'])->name('cmi');
        Route::prefix('/checkout/cmi')->name('cmi.')->withoutMiddleware([VerifyCsrfToken::class])->group(function () {
            Route::post('/', [CMIController::class, 'paymentCallback'])->name('callback');
            Route::post('/ok/{number}', [CMIController::class, 'ok'])->name('ok');
            Route::post('/fail', [CMIController::class, 'fail'])->name('fail');
        });
    });
