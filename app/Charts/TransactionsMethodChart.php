<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Transaction;

class TransactionsMethodChart extends Chart
{
    public function __construct()
    {
        parent::__construct();

        $methods = Transaction::selectRaw("method, COUNT(*) as total")
            ->groupBy('method')
            ->pluck('total', 'method');

        $this->labels($methods->keys()->toArray());

        $this->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
        ]);


        $this->dataset('Transactions by Method', 'pie', $methods->values()->toArray())
            ->backgroundColor(['#3B82F6', '#F97316', '#10B981', '#EF4444']); // customize colors
    }
}
