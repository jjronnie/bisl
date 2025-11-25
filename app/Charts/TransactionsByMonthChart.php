<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionsByMonthChart extends Chart
{
    public function __construct()
    {
        parent::__construct();

        $currentYear = now()->year;

        // Initialize all months with 0
        $months = collect([
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ]);

        // Get transaction counts grouped by month
        $transactions = Transaction::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month');

        // Map counts to months
        $monthlyData = $months->map(function ($label, $index) use ($transactions) {
            // $index + 1 because MONTH() is 1-based
            return $transactions[$index + 1] ?? 0;
        });

        $this->labels($months->toArray());



    $this->dataset('Transactions per Month', 'line', $monthlyData)
    ->backgroundColor('rgba(59, 130, 246, 0.2)')
    ->options([
        'borderColor' => '#3B82F6',   // <-- move borderColor here
        'fill' => true,
        'tension' => 0.3,
    ]);


 
    }
}
