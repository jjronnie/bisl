<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Loan;

class LoanTierChart extends Chart
{
    public function __construct()
    {
        parent::__construct();

        // Join loans to members and group by tier
        $tiers = Loan::selectRaw("members.tier as tier, COUNT(loans.id) as total")
            ->join('members', 'loans.member_id', '=', 'members.id')
            ->groupBy('members.tier')
            ->pluck('total', 'tier');

        // Labels
        $this->labels(
            $tiers->keys()
                ->map(fn($tier) => ucfirst($tier))
                ->toArray()
        );

        // Chart options
        $this->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
        ]);

        // Dataset
        $this->dataset('Loans by Tier', 'pie', $tiers->values()->toArray())
            ->backgroundColor([
                '#3B82F6',
                '#10B981',
                '#F97316',
                '#EF4444',
                '#6366F1'
            ]);
    }
}
