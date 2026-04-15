<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BulkSmsController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SmsLogController;
use App\Http\Controllers\Admin\SmsSettingController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TransactionReversalController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanDocumentController;
use App\Http\Controllers\Member\MemberController as Member;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified', 'pwc', 'role:admin|superadmin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
            ->name('dashboard');

        // Admin Member Routes
        Route::resource('members', MemberController::class);
        Route::patch('/members/{member}/suspend', [MemberController::class, 'suspend'])->name('members.suspend');
        Route::patch('/members/{member}/unsuspend', [MemberController::class, 'unsuspend'])->name('members.unsuspend');
        Route::get('/members/{member}/transactions', [MemberController::class, 'transactions'])->name('members.transactions.index');

        Route::post('/interest/update', [MemberController::class, 'updateMonthlyInterest'])->name('interest.update');
        Route::get('/interest/ledger', [MemberController::class, 'interest'])->name('interest.ledger');

        // Admins
        Route::resource('admins', AdminController::class);
        Route::patch('/admins/{admin}/suspend', [AdminController::class, 'suspend'])->name('suspend');
        Route::patch('/admins/{admin}/unsuspend', [AdminController::class, 'unsuspend'])->name('unsuspend');

        // SMS Settings (superadmin only)
        Route::middleware('role:superadmin')->group(function () {
            Route::get('/sms-settings', [SmsSettingController::class, 'index'])->name('sms-settings.index');
            Route::patch('/sms-settings', [SmsSettingController::class, 'update'])->name('sms-settings.update');

            // SMS Logs
            Route::get('/sms-logs', [SmsLogController::class, 'index'])->name('sms-logs.index');

            // Bulk SMS
            Route::get('/bulk-sms', [BulkSmsController::class, 'index'])->name('bulk-sms.index');
            Route::get('/bulk-sms/create', [BulkSmsController::class, 'create'])->name('bulk-sms.create');
            Route::get('/bulk-sms/{id}', [BulkSmsController::class, 'show'])->name('bulk-sms.show');
            Route::post('/bulk-sms/send', [BulkSmsController::class, 'send'])->name('bulk-sms.send');
            Route::get('/bulk-sms/{id}/status', [BulkSmsController::class, 'status'])->name('bulk-sms.status');
            Route::post('/bulk-sms/{id}/resend', [BulkSmsController::class, 'resend'])->name('bulk-sms.resend');
            Route::post('/bulk-sms/{id}/cancel', [BulkSmsController::class, 'cancel'])->name('bulk-sms.cancel');
            Route::delete('/bulk-sms/{id}', [BulkSmsController::class, 'destroy'])->name('bulk-sms.destroy');
        });

        // Member delete (superadmin only) - frontend protection already in place

        Route::resource('loans', LoanController::class);
        Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
        Route::post('/loans/{loan}/disburse', [LoanController::class, 'disburse'])->name('loans.disburse');
        Route::post('/payments', [LoanController::class, 'disburse'])->name('payments.create');

        Route::prefix('loans/{loan}')->group(function () {
            Route::post('/documents', [LoanDocumentController::class, 'store'])->name('loans.documents.store');
        });

        Route::get('loan-documents/{document}/download', [LoanDocumentController::class, 'download'])->name('loan-documents.download');

        Route::delete('loan-documents/{document}', [LoanDocumentController::class, 'destroy'])
            ->name('loan-documents.destroy');

        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');

        // Route to handle the submission of the payment form
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

        // Transaction Reversals
        Route::get('/transactions/reversal', [TransactionReversalController::class, 'index'])
            ->name('transactions.reversal.index');

        Route::middleware('role:superadmin')->group(function () {
            Route::get('/transactions/reversal/create', [TransactionReversalController::class, 'create'])
                ->name('transactions.reversal.create');
            Route::get('/transactions/reversal/find', [TransactionReversalController::class, 'find'])
                ->name('transactions.reversal.find');
            Route::post('/transactions/reversal/{transaction}/process', [TransactionReversalController::class, 'process'])
                ->name('transactions.reversal.process');

            Route::post('/transactions/{transaction}/reverse', [TransactionController::class, 'reverse'])
                ->name('transactions.reverse');
        });

        Route::resource('transactions', TransactionController::class)->only([
            'index',
            'create',
            'store',
            'show',
        ]);
        Route::resource('transfers', TransferController::class);

        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    });

Route::middleware(['auth', 'verified', 'pwc', 'role:user'])->group(function () {
    Route::get('member/dashboard', [DashboardController::class, 'memberDashboard'])->name('member.dashboard');
    Route::get('/transactions', [Member::class, 'index'])->name('member.transactions');
    Route::get('/notifications', [Member::class, 'notifications'])->name('member.notifications');
    Route::get('/loans', [Member::class, 'loans'])->name('member.loans');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});

require __DIR__.'/auth.php';
