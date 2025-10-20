<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

 

  Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
     Route::get('user/dashboard', [DashboardController::class, 'index'])->name('dashboard');
  
});


Route::middleware(['auth', 'verified', 'role:user|admin'])->group(function () {
  
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';







