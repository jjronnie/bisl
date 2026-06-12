<?php

namespace App\Services;

use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\PayrollSavingsContribution;
use App\Models\PayrollSetting;
use Illuminate\Support\Facades\DB;

class SavingsContributionService
{
    public function __construct(
        protected TransactionService $transactionService,
    ) {}

    public function calculateContribution(float $netSalary): float
    {
        return round($netSalary * PayrollSetting::savingsRate(), 2);
    }

    public function depositToSavings(PayrollRun $payrollRun, PayrollPeriod $period): PayrollSavingsContribution
    {
        return DB::transaction(function () use ($payrollRun, $period) {
            $profile = $payrollRun->payrollProfile;
            $member = $profile->member;

            $transaction = $this->transactionService->create([
                'member_id' => $member->id,
                'account' => 'savings',
                'transaction_type' => 'deposit',
                'amount' => $payrollRun->savings_contribution,
                'method' => 'payroll',
                'remarks' => "Payroll savings contribution for {$period->month}/{$period->year}",
            ]);

            $contribution = PayrollSavingsContribution::create([
                'payroll_run_id' => $payrollRun->id,
                'member_id' => $member->id,
                'amount' => $payrollRun->savings_contribution,
                'posted_to_savings' => true,
                'posted_at' => now(),
            ]);

            return $contribution;
        });
    }
}
