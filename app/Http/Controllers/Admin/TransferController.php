<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Transfer;
use Illuminate\Http\Request;

use App\Models\SaccoAccount;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

          $transfers = Transfer::with('saccoAccount',  )
            ->latest()
            ->paginate(50);

            $accountFields = [
    'operational' => 'Operational Account',
    'loan_interest' => 'Loan Interest',
    'loan_protection_fund' => 'Loan Protection Fund',
    'member_interest' => 'Member Interest',
    'member_savings' => 'Member Savings'
];
        return view('admin.transfers.index', compact('transfers', 'accountFields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        // Fetch list of valid columns from sacco_accounts table
        $validAccounts = [
            'operational',
            'loan_interest',
            'loan_protection_fund',
            'member_interest',
            'member_savings'
        ];

        // Validate input
        $validated = $request->validate([
            'from_account' => 'required|string|in:' . implode(',', $validAccounts),
            'to_account' => 'required|string|in:' . implode(',', $validAccounts),
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validated['from_account'] === $validated['to_account']) {
            return back()->withErrors(['Invalid transfer. Accounts must be different']);
        }

        

        return DB::transaction(function () use ($validated) {

            $sacco = SaccoAccount::first();
            $from = $validated['from_account'];
            $to = $validated['to_account'];
            $amount = $validated['amount'];

            // Balances before
            $fromBefore = $sacco->$from;
            $toBefore = $sacco->$to;

            if ($fromBefore < $amount) {
                return back()->withErrors(['Insufficient funds in '.$from.' account']);
            }

            // Update account balances
            $sacco->$from -= $amount;
            $sacco->$to += $amount;
            $sacco->save();

            // Balances after
            $fromAfter = $sacco->$from;
            $toAfter = $sacco->$to;

            // Create transfer record
            Transfer::create([
                'transfer_id' => generateTransactionId(), // your helper
                'from_account' => $from,
                'to_account' => $to,
                'amount' => $amount,

                'from_balance_before' => $fromBefore,
                'from_balance_after' => $fromAfter,
                'to_balance_before' => $toBefore,
                'to_balance_after' => $toAfter,

                'transferred_by' => auth()->id(),
                'reason' => $validated['reason'],
            ]);

            return back()->with('success', 'Transfer successful');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Transfer $transfer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transfer $transfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transfer $transfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transfer $transfer)
    {
        //
    }
}
