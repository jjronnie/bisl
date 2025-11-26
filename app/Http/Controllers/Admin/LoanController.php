<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Helpers\LoanHelper;
use App\Services\LoanService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class LoanController extends Controller
{

    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    /**
     * Display a listing of the resource.
     */



    public function index()
    {
        // 1. Fetch Paginated Loans (for the table)
        // Ensure necessary relationships are loaded
        $loans = Loan::with(['member', 'member.savingsAccount'])
                      ->latest()
                      ->paginate(10);
                      

        // Define status groups for outstanding loans (loans actively being repaid or due)
        $activeStatuses = ['approved', 'disbursed', 'active', 'defaulted', 'default_pending'];

        // 2. Calculate Summary Statistics
        $stats = [
            // Pending Applications
            'total_pending_count' => Loan::where('status', 'pending')->count(),
            'total_pending_amount' => Loan::where('status', 'pending')->sum('amount'),
            
            // Loans Outstanding (Active, Disbursed, Defaulted)
            // This is the total principal amount currently owed or awaiting disbursement
            'total_outstanding_count' => Loan::whereIn('status', $activeStatuses)->count(),
            'total_outstanding_amount' => Loan::whereIn('status', $activeStatuses)->sum('amount'),
            
            // Completed Loans (Lifetime)
            'total_completed_count' => Loan::where('status', 'completed')->count(),
            'total_completed_amount' => Loan::where('status', 'completed')->sum('amount'),
            
            // Rejected Loans
            'total_rejected_count' => Loan::where('status', 'rejected')->count(),
        ];
        
        // Add overall totals
        $stats['total_loan_count'] = Loan::count();
        // You might also want total disbursed amount lifetime:
        $stats['total_disbursed_lifetime'] = Loan::whereIn('status', array_merge($activeStatuses, ['completed']))->sum('amount');


        return view('admin.loans.index', compact('loans', 'stats'));
    }












    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::with('savingsAccount:id,member_id,account_number')
            ->whereDoesntHave('loans', function ($q) {
                $q->whereIn('status', ['approved', 'disbursed', 'active', 'defaulted']);
            })
            ->orderBy('name')
            ->get();

        return view('admin.loans.create', compact('members'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:12|max:24',
            'period' => 'required|integer|min:1', // Duration in months
            'loan_type' => 'required|string', // e.g., 'standard', 'priority'
            'purpose' => 'required|string|max:255',
            'application_date' => 'required|date',
                 'notes' => 'nullable|string'
        ]);

        try {
            // 2. Call Service
            $loan = $this->loanService->createLoan($validated, Auth::id());

            // 3. Success Response (Redirect to show page to see amortization)
            return redirect()
                ->route('admin.loans.show', $loan->id)
                ->with('success', 'Loan application created and amortization schedule generated.');

        } catch (Exception $e) {
            // 4. Handle Business Logic Failures (Tier issues, existing loans)
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        $loan->load(['member', 'installments']);

        return view('admin.loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        return view('admin.loans.edit', compact('loan'));
    }




     public function approve(Loan $loan)
    {
        try {
            $this->loanService->approve($loan);
            return redirect()
                ->route('admin.loans.show', $loan)
                ->with('success', "Loan #{$loan->loan_number} approved successfully. Ready for disbursement.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Management Action: Reject Loan
     */
    public function reject(Loan $loan)
    {
        try {
            $this->loanService->reject($loan);
            return redirect()
                ->route('admin.loans.show', $loan)
                ->with('success', "Loan #{$loan->loan_number} rejected.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Management Action: Disburse Loan
     * IMPORTANT: This updates all installment due dates.
     */
    public function disburse(Loan $loan)
    {
        try {
            $this->loanService->disburse($loan);
            return redirect()
                ->route('admin.loans.show', $loan)
                ->with('success', "Loan #{$loan->loan_number} disbursed and is now ACTIVE. Installment due dates have been adjusted.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        //
    }
}
