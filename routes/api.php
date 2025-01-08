<?php

use App\Http\Controllers\{ContactVerifiedController, PaymentAcknowledgedController};
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('contact-verified', ContactVerifiedController::class)->name('contact-verified');
Route::post('payment-acknowledged', PaymentAcknowledgedController::class)->name('payment-acknowledged');
