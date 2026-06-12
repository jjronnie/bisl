<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\TransactionDocument;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $query = Transaction::with(['member.savingsAccount', 'createdBy', 'reversals', 'documents']);

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
        $members = Member::with('savingsAccount', 'salaryAccount')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'account_number' => $member->savingsAccount?->account_number,
                    'accounts' => collect([
                        [
                            'key' => 'savings',
                            'label' => 'Savings',
                            'balance' => (float) ($member->savingsAccount?->balance ?? 0),
                            'allowed_types' => ['deposit', 'withdrawal'],
                            'icon' => 'coins',
                            'description' => 'Deposit or withdraw savings',
                        ],
                        [
                            'key' => 'loan_protection_fund',
                            'label' => 'Loan Protection Fund',
                            'balance' => (float) ($member->savingsAccount?->loan_protection_fund ?? 0),
                            'allowed_types' => ['deposit'],
                            'icon' => 'shield',
                            'description' => 'Deposit only',
                        ],
                        ...($member->salaryAccount ? [
                            [
                                'key' => 'salary',
                                'label' => 'Salary Account',
                                'balance' => (float) $member->salaryAccount->balance,
                                'allowed_types' => ['withdrawal'],
                                'icon' => 'wallet',
                                'description' => 'Withdraw earned salary',
                            ],
                        ] : []),
                    ]),
                ];
            });

        return view('admin.transactions.create', compact('members'));
    }

    public function store(Request $request, TransactionService $service)
    {
        $request->merge([
            'amount' => $request->filled('amount') ? (int) str_replace(',', '', $request->amount) : null,
        ]);

        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'account' => ['required', 'in:savings,loan_protection_fund,salary'],
            'transaction_type' => ['required', 'in:deposit,withdrawal'],
            'amount' => ['required', 'numeric', 'min:1'],
            'method' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
        ]);

        $member = Member::with('savingsAccount', 'salaryAccount')->find($validated['member_id']);

        if (! $member) {
            return back()->withInput()->withErrors(['member_id' => 'Member not found.']);
        }

        if ($validated['account'] === 'salary' && ! $member->salaryAccount) {
            return back()->withInput()->withErrors(['account' => 'This member does not have a salary account.']);
        }

        if ($validated['account'] === 'loan_protection_fund' && $validated['transaction_type'] === 'withdrawal') {
            return back()->withInput()->withErrors([
                'transaction_type' => 'Withdrawals are not allowed from Loan Protection Fund.',
            ]);
        }

        if ($validated['account'] === 'salary' && $validated['transaction_type'] === 'deposit') {
            return back()->withInput()->withErrors([
                'transaction_type' => 'Deposits are not allowed to Salary Account. Salary is deposited automatically via payroll.',
            ]);
        }

        try {
            $transaction = $service->create($validated);

            $docs = $request->input('documents', []);
            $docFiles = $request->file('documents', []);

            foreach ($docs as $index => $doc) {
                $file = $docFiles[$index]['file'] ?? null;

                if (! $file || ! $file->isValid()) {
                    continue;
                }

                $path = $file->store('transaction_documents');

                TransactionDocument::create([
                    'transaction_id' => $transaction->id,
                    'name' => $doc['name'],
                    'notes' => $doc['notes'] ?? null,
                    'file_path' => $path,
                ]);
            }

            return redirect()
                ->route('admin.transactions.index')
                ->with('success', 'Transaction recorded successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([$e->getMessage()]);
        }
    }

    public function show(Transaction $transaction)
    {
        return redirect()->route('admin.transactions.index');
    }

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
                ->withErrors([$e->getMessage()]);
        }
    }
}
