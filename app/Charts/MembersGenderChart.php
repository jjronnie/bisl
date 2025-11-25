<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Member;

class MembersGenderChart extends Chart
{
    public function __construct()
    {
        parent::__construct();

        $this->labels(['Male', 'Female']);

        $genderCounts = Member::selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender');

        $this->dataset('Members by Gender', 'pie', [
            $genderCounts['male'] ?? 0,
            $genderCounts['female'] ?? 0
        ])->backgroundColor(['#3B82F6', '#F472B6']); // Blue for male, pink for female

        $this->options([
            'responsive' => true,
            'maintainAspectRatio' => false
        ]);
    }
}
