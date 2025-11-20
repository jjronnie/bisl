<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\Member;


use App\Services\LoanService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{

    protected LoanService $service;

    public function __construct(LoanService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loans = Loan::latest()->paginate(20);
        return view('admin.loans.index', compact('loans'));
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
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',

            'principal_amount' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0',
            'interest_type' => 'required|in:flat,reducing_balance',

            'duration_months' => 'required|integer|min:1',

            'penalty_rate' => 'nullable|numeric|min:0',

            'purpose' => 'nullable|string|max:255',

            'application_date' => 'required|date',
            'approval_date' => 'nullable|date',
            'disbursement_date' => 'nullable|date',
            'due_date' => 'nullable|date'
        ]);

        $validated['created_by'] = Auth::id();

        try {
            $loan = $this->service->create($validated);
            return redirect()->route('admin.loans.show', $loan->id)
                ->with('success', 'Loan created successfully.');
        } catch (\Throwable $e) {
            report($e);
            throw ValidationException::withMessages([
                'error' => 'Failed to create loan. Please try again.'
            ]);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        return view('loans.show', compact('loan'));
    }

    public function edit(Loan $loan)
    {
        return view('loans.edit', compact('loan'));
    }

    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'interest_rate' => 'nullable|numeric|min:0',
            'penalty_rate' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,approved,disbursed,active,completed,rejected,defaulted',
            'amount_paid_to_date' => 'nullable|numeric|min:0',
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $validated['approved_by'] = $request->status === 'approved' ? Auth::id() : $loan->approved_by;

        try {
            $updated = $this->service->update($loan, $validated);

            return redirect()->route('loans.show', $loan->id)
                ->with('success', 'Loan updated successfully.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            throw ValidationException::withMessages([
                'error' => 'Failed to update loan.'
            ]);
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
