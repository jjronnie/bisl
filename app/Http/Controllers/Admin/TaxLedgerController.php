<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Models\TaxLedgerEntry;
use Illuminate\Http\Request;

class TaxLedgerController extends Controller
{
    public function index(Request $request)
    {
        $query = TaxLedgerEntry::with(['payrollRun.payrollProfile.member', 'payrollRun.payrollPeriod']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payroll_period_id')) {
            $query->whereHas('payrollRun', function ($q) use ($request) {
                $q->where('payroll_period_id', $request->payroll_period_id);
            });
        }

        $entries = $query->latest()->paginate(20)->withQueryString();

        $totalPending = TaxLedgerEntry::where('status', 'pending')->sum('amount');
        $totalRemitted = TaxLedgerEntry::where('status', 'remitted')->sum('amount');

        $periods = PayrollPeriod::latest('year')->latest('month')->get();

        return view('admin.payroll.settings.tax-ledger', compact('entries', 'totalPending', 'totalRemitted', 'periods'));
    }

    public function remit(TaxLedgerEntry $entry)
    {
        if ($entry->status === 'remitted') {
            return back()->with('error', 'This entry has already been remitted.');
        }

        $entry->update(['status' => 'remitted', 'remitted_at' => now()]);

        return back()->with('success', 'Tax entry remitted successfully.');
    }
}
