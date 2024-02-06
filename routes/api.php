<?php

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
    Route::post('/callback', [PaymentController::class, 'callback'])->name('callback');
    Route::controller(PaymentController::class)->middleware('payment.incoming')->group(function () {
        Route::post('/card/widget', 'getWidget')->name('card.widget');
        Route::post('/card/validate', 'cardValidate')->name('card.validate');
        Route::post('/', 'payment')->name('payment');
    });
});
