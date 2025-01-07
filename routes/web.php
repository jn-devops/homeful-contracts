<?php

use App\Http\Controllers\Contract\{AvailController, ConsultController, VerifyController};
use App\Http\Controllers\RegisterContactController;
use App\Http\Controllers\VerifyContactController;
use App\Http\Controllers\ContactVerifiedController;
use App\Http\Controllers\ContactOnboardedController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('register-contact', RegisterContactController::class)->name('register-contact');
Route::resource('consult', ConsultController::class)->only(['create', 'store']);
Route::resource('avail', AvailController::class)->only(['create', 'store']);
Route::get('verify-contact', VerifyContactController::class)->name('verify-contact');
Route::resource('verify', VerifyController::class)->only(['create', 'store']);
Route::get('contact-onboarded', ContactOnboardedController::class)->name('contact-onboarded');

require __DIR__.'/auth.php';
