<?php

namespace App\Services;

use App\Models\AllowanceType;
use App\Models\PayrollProfile;

class AllowanceCalculatorService
{
    const float MEETING_ALLOWANCE_PER_MEETING = 5000;

    public function calculateQualificationAllowance(string $qualificationLevel): float
    {
        $code = match ($qualificationLevel) {
            'certificate' => 'certificate',
            'diploma' => 'diploma',
            'bachelors' => 'bachelors',
            'masters' => 'masters',
            'phd' => 'phd',
            default => null,
        };

        if ($code === null) {
            return 0;
        }

        $allowanceType = AllowanceType::where('code', $code)->first();

        return $allowanceType?->amount ?? 0;
    }

    public function calculateRecognitionAllowance(string $recognitionLevel): float
    {
        $code = match ($recognitionLevel) {
            'appreciation' => 'appreciation',
            'golden_medal' => 'golden_medal',
            default => null,
        };

        if ($code === null) {
            return 0;
        }

        $allowanceType = AllowanceType::where('code', $code)->first();

        return $allowanceType?->amount ?? 0;
    }

    public function calculateMeetingAllowance(int $meetingsAttended): float
    {
        if ($meetingsAttended <= 0) {
            return 0;
        }

        $allowanceType = AllowanceType::where('code', 'meeting')->first();
        $rate = $allowanceType?->amount ?? self::MEETING_ALLOWANCE_PER_MEETING;

        return round($meetingsAttended * $rate, 2);
    }

    public function calculateOtherAllowances(PayrollProfile $profile): float
    {
        $allowances = $profile->allowances()
            ->where('is_active', true)
            ->where('effective_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->get();

        return round($allowances->sum('amount'), 2);
    }
}
