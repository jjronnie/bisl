<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;

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
    public function index(Request $request)
    {
        $query = Transaction::with(['member.savingsAccount', 'createdBy', 'reversals']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('member', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type') && in_array($request->type, ['deposit', 'withdrawal', 'reversal'])) {
            $query->where('transaction_type', $request->type);
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $members = Member::with('savingsAccount')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('admin.transactions.create', compact('members'));
    }

    public function store(Request $request, TransactionService $service)
    {
        $request->merge([
            'amount' => $request->filled('amount') ? (int) str_replace(',', '', $request->amount) : null,
        ]);

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'transaction_type' => 'required|in:deposit,withdrawal',
            'account' => 'required',
            'amount' => 'required|numeric|min:1',
            'method' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        try {
            $transaction = $service->create($validated);

            return redirect()
                ->route('admin.transactions.index')
                ->with('success', 'Transaction recorded successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        return redirect()->route('admin.transactions.index');
    }

    /**
     * Reverse a deposit transaction.
     */
    public function reverse(Request $request, Transaction $transaction, TransactionService $service)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $reversal = $service->reverse($transaction, $validated['reason']);

            return redirect()
                ->back()
                ->with('success', 'Transaction reversed successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors($e->getMessage());
        }
    }
}
