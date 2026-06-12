<?php

namespace App\Jobs;

use App\Models\PayrollAttendance;
use App\Models\PayrollMeetingAttendance;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use App\Services\PayrollPostingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePayrollJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public PayrollPeriod $period,
        public array $profileIds,
    ) {}

    public function handle(PayrollPostingService $postingService): void
    {
        $profiles = PayrollProfile::whereIn('id', $this->profileIds)
            ->where('is_active', true)
            ->get();

        foreach ($profiles as $profile) {
            try {
                $attendance = PayrollAttendance::where('payroll_profile_id', $profile->id)
                    ->where('payroll_period_id', $this->period->id)
                    ->first();

                if (! $attendance) {
                    Log::warning("No attendance record for profile {$profile->id} in period {$this->period->id}");

                    continue;
                }

                $meetingAttendance = PayrollMeetingAttendance::where('payroll_profile_id', $profile->id)
                    ->where('payroll_period_id', $this->period->id)
                    ->first();

                $postingService->generate($profile, $this->period, $attendance, $meetingAttendance);
            } catch (\Exception $e) {
                Log::error("Payroll generation failed for profile {$profile->id}: {$e->getMessage()}");
            }
        }

        $this->period->update(['status' => 'draft']);
    }
}
