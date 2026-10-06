<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ConfirmBookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeleteBookingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookingController::class, 'create'])->name('home');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/success', [BookingController::class, 'success'])->name('bookings.success');

Route::middleware(['auth', 'verified', 'can:admin'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::patch('/bookings/{booking}/confirm', ConfirmBookingController::class)->name('bookings.confirm');
    Route::delete('/bookings/{booking}', DeleteBookingController::class)->name('bookings.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
