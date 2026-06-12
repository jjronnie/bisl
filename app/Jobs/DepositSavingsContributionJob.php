<?php

namespace App\Jobs;

use App\Models\PayrollSavingsContribution;
use App\Services\SavingsContributionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DepositSavingsContributionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public PayrollSavingsContribution $contribution,
    ) {}

    public function handle(SavingsContributionService $savingsService): void
    {
        $payrollRun = $this->contribution->payrollRun;

        $savingsService->depositToSavings(
            $payrollRun,
            $payrollRun->payrollPeriod,
        );
    }
}
