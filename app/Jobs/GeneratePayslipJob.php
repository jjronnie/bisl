<?php

namespace App\Jobs;

use App\Models\PayrollRun;
use App\Services\PayslipService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePayslipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public PayrollRun $payrollRun,
    ) {}

    public function handle(PayslipService $payslipService): void
    {
        try {
            $payslipData = $payslipService->generatePayslipData($this->payrollRun);

            Log::info("Payslip generated for payroll run {$this->payrollRun->id}", [
                'employee' => $payslipData['employee_name'],
                'period' => $payslipData['period'],
                'net_salary' => $payslipData['net_salary'],
            ]);

        } catch (\Exception $e) {
            Log::error("Payslip generation failed for run {$this->payrollRun->id}: {$e->getMessage()}");
        }
    }
}
