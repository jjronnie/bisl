<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

 

  Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
     Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');
     Route::resource('members', MemberController::class);
  
});


Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
     Route::get('member/dashboard', [DashboardController::class, 'memberDashboard'])->name('member.dashboard');
  
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/force-change-password', [ProfileController::class, 'edit'])->name('password.change');
    Route::put('/force-update-password', [ProfileController::class, 'updatePasswordForce'])
         ->name('password.update.forced');
});

require __DIR__.'/auth.php';







