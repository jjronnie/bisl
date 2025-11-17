<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Transaction;
use Illuminate\Http\Request;

use App\Models\Member;
use App\Http\Requests\StoreTransactionRequest;
use App\Services\TransactionService;
use App\Exceptions\InsufficientFundsException;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
      protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with('member:id,name,sacco_member_id')
            ->latest()
            ->paginate(20);

        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
public function create(Request $request)
    {
        // Get all active members, ordered by name
        // Eager load the savingsAccount and select only the columns we need
        $members = Member::with('savingsAccount:id,member_id,account_number')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();
        
        return view('admin.transactions.create', compact('members'));
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'savings_account_id' => 'required|exists:savings_accounts,id',
            'transaction_type' => 'required|in:deposit,withdrawal,loan_disbursement,loan_repayment,fee,other',
            'amount' => 'required|numeric|min:0.01',
            'is_debit' => 'required|boolean',
        ]);

        try {
            $transaction = $this->transactionService->createTransaction($request->all());
            return response()->json(['success' => true, 'transaction' => $transaction]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    /**
     * Display the specified resource.
     */
  public function show(Transaction $transaction)
    {
        // Eager load related data
        $transaction->load(['member', 'savingsAccount', 'loan', 'transactedBy']);
        
        return view('transactions.show', compact('transaction'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
