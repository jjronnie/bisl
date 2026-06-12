<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NssfLedgerEntry;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;

class NssfLedgerController extends Controller
{
    public function index(Request $request)
    {
        $query = NssfLedgerEntry::with(['payrollRun.payrollProfile.member', 'payrollRun.payrollPeriod']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payroll_period_id')) {
            $query->whereHas('payrollRun', function ($q) use ($request) {
                $q->where('payroll_period_id', $request->payroll_period_id);
            });
        }

        $entries = $query->latest()->paginate(20)->withQueryString();

        $totalPending = NssfLedgerEntry::where('status', 'pending')->sum('total_amount');
        $totalRemitted = NssfLedgerEntry::where('status', 'remitted')->sum('total_amount');

        $periods = PayrollPeriod::latest('year')->latest('month')->get();

        return view('admin.payroll.settings.nssf-ledger', compact('entries', 'totalPending', 'totalRemitted', 'periods'));
    }

    public function remit(NssfLedgerEntry $entry)
    {
        if ($entry->status === 'remitted') {
            return back()->with('error', 'This entry has already been remitted.');
        }

        $entry->update(['status' => 'remitted', 'remitted_at' => now()]);

        return back()->with('success', 'NSSF entry remitted successfully.');
    }
}
