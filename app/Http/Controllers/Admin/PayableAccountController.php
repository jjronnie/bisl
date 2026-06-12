<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayableAccount;
use Illuminate\Http\Request;

class PayableAccountController extends Controller
{
    public function tax()
    {
        $account = PayableAccount::tax();
        $withdrawals = $account->withdrawals()->with('withdrawnBy')->latest('withdrawn_at')->paginate(20);

        return view('admin.payroll.ledgers.tax', compact('account', 'withdrawals'));
    }

    public function nssf()
    {
        $account = PayableAccount::nssf();
        $withdrawals = $account->withdrawals()->with('withdrawnBy')->latest('withdrawn_at')->paginate(20);

        return view('admin.payroll.ledgers.nssf', compact('account', 'withdrawals'));
    }

    public function withdraw(Request $request, PayableAccount $account)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        if ((float) $validated['amount'] > (float) $account->balance) {
            return back()->with('error', 'Insufficient balance. Available: UGX '.number_format($account->balance, 0));
        }

        $account->withdrawals()->create([
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'withdrawn_by' => auth()->id(),
            'withdrawn_at' => now(),
        ]);

        $account->decrement('balance', (float) $validated['amount']);
        $account->increment('total_withdrawn', (float) $validated['amount']);

        return back()->with('success', 'Withdrawal of UGX '.number_format((float) $validated['amount'], 0).' recorded.');
    }
}
