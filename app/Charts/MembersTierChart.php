<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Member;

class MembersTierChart extends Chart
{
    public function __construct()
    {
        parent::__construct();

        $this->labels(['Silver', 'Gold']); 

        $this->options([
    'responsive' => true,
    'maintainAspectRatio' => false,
]);


        $tierCounts = Member::selectRaw("tier, COUNT(*) as total")
            ->groupBy('tier')
            ->pluck('total', 'tier');

        $this->dataset('Members by Tier', 'pie', [
            $tierCounts['silver'] ?? 0,
            $tierCounts['gold'] ?? 0
        ])->backgroundColor(['#9ca3af', '#f59e0b']); // gray for silver, amber for gold
    }
}
