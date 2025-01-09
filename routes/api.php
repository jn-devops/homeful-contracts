<?php

use App\Http\Controllers\{ContactVerifiedController, PaymentCollectedController};
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('contact-verified', ContactVerifiedController::class)->name('contact-verified');
Route::post('payment-collected', PaymentCollectedController::class)->name('payment-collected');
