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
        $transactions = Transaction::with('member',  'savingsAccount','creator')
            ->latest()
            ->paginate(50);

              $members = Member::with('savingsAccount:id,member_id,account_number')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('admin.transactions.index', compact('transactions', 'members'));
    }


public function store(Request $request, TransactionService $service)
{
   
    $validated = $request->validate([
        'member_id' => 'required|exists:members,id',
        'transaction_type' => 'required|in:deposit,withdrawal',
        'amount' => 'required|numeric|min:1',
        'method' => 'nullable|string',
        'description' => 'nullable|string|max:255',
        'remarks' => 'nullable|string'
    ]);

    try {
        $transaction = $service->create($validated);



        return redirect()->back()->with('success', 'Transaction successful.');
    } catch (\Exception $e) {
        return redirect()->back()->withErrors($e->getMessage());
    }
}
    /**
     * Display the specified resource.
     */
  public function show(Transaction $transaction)
    {
        // Eager load related data
        $transaction->load(['member', 'savingsAccount','creator']);
        
        return view('admin.transactions.show', compact('transaction'));
    }

}
