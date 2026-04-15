<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reversal;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionReversalController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index(Request $request): View
    {
        $query = Reversal::with(['transaction', 'transaction.member', 'transaction.member.savingsAccount', 'reversedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('transaction', function ($q) use ($search) {
                        $q->where('reference_number', 'like', "%{$search}%")
                            ->orWhereHas('member', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $reversals = $query->latest()->paginate(20);
        $years = Reversal::selectRaw('YEAR(created_at) as year')->distinct()->orderByDesc('year')->pluck('year');

        return view('admin.transactions.reversal.index', compact('reversals', 'years'));
    }

    public function create(Request $request): View
    {
        $searchQuery = $request->get('transaction_id');

        return view('admin.transactions.reversal.create', compact('searchQuery'));
    }

    public function find(Request $request): View|RedirectResponse
    {
        $request->validate([
            'transaction_id' => 'required|string|min:3',
        ]);

        $transaction = Transaction::with(['member', 'member.savingsAccount', 'createdBy', 'reversals.reversedBy'])
            ->where('reference_number', $request->transaction_id)
            ->first();

        if (! $transaction) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['transaction_id' => 'Transaction not found. Please verify the transaction ID.']);
        }

        $eligibility = $this->checkEligibility($transaction);

        return view('admin.transactions.reversal.create', [
            'transaction' => $transaction,
            'eligibility' => $eligibility,
            'searchQuery' => $request->transaction_id,
        ]);
    }

    public function process(Request $request, Transaction $transaction): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $eligibility = $this->checkEligibility($transaction);

        if (! $eligibility['eligible']) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['transaction_id' => $eligibility['reason']]);
        }

        try {
            $this->transactionService->reverse($transaction, $request->reason);

            return redirect()
                ->route('admin.transactions.reversal.index')
                ->with('success', 'Transaction reversed successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['transaction_id' => $e->getMessage()]);
        }
    }

    private function checkEligibility(Transaction $transaction): array
    {
        if ($transaction->transaction_type !== 'deposit') {
            return [
                'eligible' => false,
                'reason' => 'Only deposit transactions can be reversed.',
            ];
        }

        if ($transaction->created_at->lt(now()->subDays(14))) {
            return [
                'eligible' => false,
                'reason' => 'Transaction cannot be reversed. It is older than 2 weeks.',
            ];
        }

        if ($transaction->reversals()->exists()) {
            return [
                'eligible' => false,
                'reason' => 'Transaction has already been reversed.',
            ];
        }

        $balanceColumn = match ($transaction->account) {
            'savings' => 'balance',
            'loan_protection_fund' => 'loan_protection_fund',
            default => null,
        };

        if ($balanceColumn && $transaction->member->savingsAccount->{$balanceColumn} < $transaction->amount) {
            return [
                'eligible' => false,
                'reason' => "Insufficient balance in {$transaction->account} account. Current balance (UGX ".number_format($transaction->member->savingsAccount->{$balanceColumn}).') is less than the deposit amount (UGX '.number_format($transaction->amount).').',
            ];
        }

        return [
            'eligible' => true,
            'reason' => null,
        ];
    }
}
