<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Services\PaymentService;
use Exception;


class PaymentController extends Controller
{

      protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
   public function create(Request $request)
    {
        // Load the loan based on the loan_id query parameter from the 'Log Payment' button
        $loan = Loan::findOrFail($request->query('loan_id'));

        // Find the next expected installment to show the amount due
        $installment = $loan->installments()
            ->whereIn('status', ['pending', 'partial', 'defaulted'])
            ->orderBy('due_date', 'asc')
            ->first();

        $amountDue = $installment 
            ? $installment->principal_amount + $installment->interest_amount + $installment->penalty_amount 
            : 0;

        return view('admin.loans.payments', compact('loan', 'installment', 'amountDue'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'payment_amount' => 'required|numeric|min:1',
            // Add fields for payment method, reference, etc. if needed
        ]);

        $loan = Loan::findOrFail($validated['loan_id']);

        try {
            // Log the payment using the service
            $updatedLoan = $this->paymentService->logPayment($loan, $validated);

            return redirect()
                ->route('admin.loans.show', $updatedLoan->id)
                ->with('success', "Payment of UGX " . number_format($validated['payment_amount'], 2) . " successfully processed. Loan status: " . ucfirst($updatedLoan->status) . ".");

        } catch (Exception $e) {
            return redirect()
                ->route('admin.loans.show', $loan->id)
                ->with('error', "Payment processing failed: " . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
