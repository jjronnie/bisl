<?php

use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
     return redirect()->route('login');
})->name('home');



Route::middleware(['auth', 'verified', 'pwc', 'role:admin'])
     ->prefix('admin')
     ->as('admin.')
     ->group(function () {

          Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
               ->name('dashboard');

          Route::resource('members', MemberController::class);
          Route::resource('admins', AdminController::class);
          Route::resource('transactions', TransactionController::class)->only([
        'index', 'create', 'store', 'show'
    ]);
    
    Route::get('/members/search', [MemberController::class, 'search'])->name('members.search');
     });



Route::middleware(['auth', 'verified', 'pwc', 'role:user'])->group(function () {
     Route::get('member/dashboard', [DashboardController::class, 'memberDashboard'])->name('member.dashboard');

});




Route::middleware('auth', )->group(function () {
     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
     // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});

require __DIR__ . '/auth.php';







