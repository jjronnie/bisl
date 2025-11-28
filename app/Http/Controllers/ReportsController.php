<?php

namespace App\Http\Controllers;

use App\Charts\LoanStatusChart;
use App\Charts\LoanTierChart;
use Illuminate\Http\Request;
use App\Charts\MembersTierChart;
use App\Charts\TransactionsMethodChart;
use App\Charts\MembersGenderChart;
use App\Charts\TransactionsByMonthChart;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\Loan;

class ReportsController extends Controller
{
    public function index()
    {

        $totalMembers = Member::count();
        // Sum all savings account balances
        $totalBalance = SavingsAccount::sum('balance');


        // Total outstanding loans (active, disbursed, defaulted)
        $outstandingLoans = Loan::whereIn('status', ['active', 'disbursed', 'defaulted']);

        $totalOutstandingLoans = $outstandingLoans->count();

     




        // Avoid division by zero
        if ($totalMembers > 0) {
            $averageBalance = $totalBalance / $totalMembers;
        } else {
            $averageBalance = 0;
        }

        // Format if needed
        $averageBalanceFormatted = number_format($averageBalance);







        $membersTierChart = new MembersTierChart();
        $transactionsMethodChart = new TransactionsMethodChart();
        $genderChart = new MembersGenderChart();
        $transactionsChart = new TransactionsByMonthChart();
        $loanStatusChart = new LoanStatusChart();
        $loanTierChart = new LoanTierChart();


        return view('admin.reports', compact(
            'membersTierChart',
            'transactionsMethodChart',
            'genderChart',
            'transactionsChart',
            'loanStatusChart',
            'loanTierChart',
            'totalMembers',
            'totalBalance',
            'totalOutstandingLoans',
            'averageBalanceFormatted'
        ));
    }
}
