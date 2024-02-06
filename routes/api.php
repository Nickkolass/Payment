<?php

use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/payment')->name('payment.')->group(function () {
    Route::post('/callback', [PaymentCallbackController::class, 'callback'])->name('callback');
    Route::controller(PaymentController::class)->middleware('payment.incoming')->group(function () {
        Route::post('/card/widget', 'getWidget')->name('card.widget');
        Route::post('/card/validate', 'cardValidate')->name('card.validate');
        Route::post('/pay', 'pay')->name('pay');
        Route::post('/payout', 'payout')->name('payout');
        Route::post('/refund', 'refund')->name('refund');
    });
});
