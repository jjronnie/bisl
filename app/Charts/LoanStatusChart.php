<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Loan;

class LoanStatusChart extends Chart
{
    public function __construct()
    {
        parent::__construct();

        // Count loans by status
        $statuses = Loan::selectRaw("status, COUNT(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status');

        // Set chart labels
        $this->labels($statuses->keys()->toArray());

        // Chart options
        $this->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
        ]);

        // Dataset with default colors
        $this->dataset('Loans by Status', 'pie', $statuses->values()->toArray())
            ->backgroundColor([
                '#3B82F6',
                '#F97316',
                '#10B981',
                '#EF4444',
                '#6366F1',
                '#F59E0B'
            ]);
    }
}
