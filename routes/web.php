<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Sementara tanpa auth
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('platforms', PlatformController::class);
Route::resource('debts', DebtController::class);
Route::post('/debts/{id}/pay', [DebtController::class, 'pay'])->name('debts.pay');

// Route yang membutuhkan login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';