<?php

namespace App\Jobs;

use App\Models\PayrollRun;
use App\Services\PayrollPostingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostPayrollTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public PayrollRun $payrollRun,
    ) {}

    public function handle(PayrollPostingService $postingService): void
    {
        $postingService->post(
            $this->payrollRun->payrollProfile,
            $this->payrollRun->payrollPeriod,
            $this->payrollRun->payrollProfile->attendance()->where('payroll_period_id', $this->payrollRun->payroll_period_id)->first(),
            $this->payrollRun->payrollProfile->meetingAttendance()->where('payroll_period_id', $this->payrollRun->payroll_period_id)->first(),
        );
    }
}
