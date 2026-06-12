<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AllowanceTypeController;
use App\Http\Controllers\Admin\BulkSmsController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\PayableAccountController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PayrollAttendanceController;
use App\Http\Controllers\Admin\PayrollDashboardController;
use App\Http\Controllers\Admin\PayrollGradeController;
use App\Http\Controllers\Admin\PayrollPeriodController;
use App\Http\Controllers\Admin\PayrollProfileController;
use App\Http\Controllers\Admin\PayrollRunController;
use App\Http\Controllers\Admin\PayrollSettingController;
use App\Http\Controllers\Admin\PayrollTaxBracketController;
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
use App\Http\Controllers\TransactionDocumentController;
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

        Route::prefix('transactions/{transaction}')->group(function () {
            Route::get('/documents', [TransactionDocumentController::class, 'index'])->name('transactions.documents');
            Route::post('/documents', [TransactionDocumentController::class, 'store'])->name('transactions.documents.store');
        });

        Route::get('transaction-documents/{document}/download', [TransactionDocumentController::class, 'download'])->name('transaction-documents.download');

        Route::delete('transaction-documents/{document}', [TransactionDocumentController::class, 'destroy'])
            ->name('transaction-documents.destroy');

        Route::resource('transfers', TransferController::class);

        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

        // Payroll Module
        Route::middleware('role:superadmin')->prefix('payroll')->name('payroll.')->group(function () {
            Route::resource('grades', PayrollGradeController::class);
            Route::resource('profiles', PayrollProfileController::class);
            Route::resource('periods', PayrollPeriodController::class);
            Route::post('/periods/{period}/generate', [PayrollPeriodController::class, 'generate'])->name('periods.generate');
            Route::post('/periods/{period}/regenerate', [PayrollPeriodController::class, 'regenerate'])->name('periods.regenerate');
            Route::post('/periods/{period}/dispatch', [PayrollPeriodController::class, 'dispatch'])->name('periods.dispatch');

            // Route::post('/periods/{period}/cancel', [PayrollPeriodController::class, 'cancel'])->name('periods.cancel');
            Route::resource('attendance', PayrollAttendanceController::class)->only(['index', 'store']);
            Route::resource('salary-history', PayrollRunController::class)->parameters(['salary-history' => 'run'])->only(['index', 'show'])->names('runs');

            // Payroll Dashboard
            Route::get('/dashboard', [PayrollDashboardController::class, 'index'])->name('dashboard');

            // Payroll Settings
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [PayrollSettingController::class, 'index'])->name('index');
                Route::put('/', [PayrollSettingController::class, 'update'])->name('update');
                Route::get('/tax-brackets', [PayrollTaxBracketController::class, 'index'])->name('tax-brackets');
                Route::post('/tax-brackets', [PayrollTaxBracketController::class, 'store'])->name('tax-brackets.store');
                Route::put('/tax-brackets/{taxBracket}', [PayrollTaxBracketController::class, 'update'])->name('tax-brackets.update');
                Route::delete('/tax-brackets/{taxBracket}', [PayrollTaxBracketController::class, 'destroy'])->name('tax-brackets.destroy');
                Route::get('/allowance-types', [AllowanceTypeController::class, 'index'])->name('allowance-types');
                Route::post('/allowance-types', [AllowanceTypeController::class, 'store'])->name('allowance-types.store');
                Route::put('/allowance-types/{allowanceType}', [AllowanceTypeController::class, 'update'])->name('allowance-types.update');
                Route::delete('/allowance-types/{allowanceType}', [AllowanceTypeController::class, 'destroy'])->name('allowance-types.destroy');
            });

            // Payroll Ledgers (aggregated payable accounts)
            Route::prefix('ledgers')->name('ledgers.')->group(function () {
                Route::get('/tax', [PayableAccountController::class, 'tax'])->name('tax');
                Route::get('/nssf', [PayableAccountController::class, 'nssf'])->name('nssf');
                Route::post('/{account}/withdraw', [PayableAccountController::class, 'withdraw'])->name('withdraw');
            });
        });

    });

Route::middleware(['auth', 'verified', 'pwc', 'role:user'])->group(function () {
    Route::get('member/dashboard', [DashboardController::class, 'memberDashboard'])->name('member.dashboard');
    Route::get('/transactions', [Member::class, 'index'])->name('member.transactions');
    Route::get('/notifications', [Member::class, 'notifications'])->name('member.notifications');
    Route::get('/loans', [Member::class, 'loans'])->name('member.loans');
    Route::get('/payroll', [Member::class, 'payroll'])->name('member.payroll');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});

require __DIR__.'/auth.php';
