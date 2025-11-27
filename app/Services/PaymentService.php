<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanInstallment;
use Illuminate\Support\Facades\DB;
use App\Models\SaccoAccount; 
use Exception;

class PaymentService
{
    /**
     * Processes an incoming payment against a loan, applying it to the oldest outstanding installment.
     *
     * @param Loan $loan The loan receiving the payment.
     * @param array $data Contains ['payment_amount']
     * @return LoanInstallment|null The installment that was fully or partially paid.
     * @throws Exception
     */
    public function logPayment(Loan $loan, array $data): ?Loan
    {
        // Use a database transaction for atomicity
        return DB::transaction(function () use ($loan, $data) {

            $paymentAmount = $data['payment_amount'];
            $paymentMethod = $data['payment_method'] ?? 'cash'; // Default if not provided
            $reference = $data['reference'] ?? 'N/A';             // Default if not provided
            
            // Fetch the single Sacco Account record
            $saccoAccount = SaccoAccount::first();
            if (!$saccoAccount) {
                 throw new Exception("SACCO operational account not found. Cannot log payment.");
            }

            // NOTE: In a complete application, a separate 'Payment' model would be created here 
            // to log the transaction (amount, method, reference, loan_id, user_id).

            // Find the oldest pending or partially paid installment
            $installment = $loan->installments()
                ->whereIn('status', ['pending', 'partial', 'defaulted'])
                ->orderBy('due_date', 'asc')
                ->first();

            if (!$installment) {
                // Check if the loan is truly completed
                if ($loan->status !== 'completed') {
                    $loan->update(['status' => 'completed']);
                }
                throw new Exception("No outstanding installments found. Loan #{$loan->loan_number} is already paid off. Overpayment not handled.");
            }

            // Calculate the total amount due for this installment (Principal + Interest + Penalty)
            $amountDue = $installment->principal_amount + $installment->interest_amount + $installment->penalty_amount;
            
            if ($paymentAmount >= $amountDue) {
                // 1. Payment is FULL or OVER
                
                // --- ACCOUNTING ALLOCATION (Applies to the full amount due) ---
                $principalPaid = $installment->principal_amount;
                $interestPaid = $installment->interest_amount;
                $penaltyPaid = $installment->penalty_amount;
                
                // 1.1. Adjust SACCO Balances
                // Principal portion returns to the lending pool
                $saccoAccount->member_savings += $principalPaid; 
                // Interest portion is recorded as revenue
                $saccoAccount->loan_interest += $interestPaid;
                // Penalty portion is recorded as operational income
                $saccoAccount->operational += $penaltyPaid;
                
                $saccoAccount->save();
                // -----------------------------------------------------------------

                $installment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    // This is where you might link to the created Payment transaction ID
                ]);

                // Recursive call or loop to handle overpayment against the NEXT installment
                $remainingPayment = $paymentAmount - $amountDue;
                if ($remainingPayment > 0) {
                    // Recalculate data for recursion (only need the remainder amount)
                    $data['payment_amount'] = $remainingPayment;
                    // Note: We use the *same* payment method/reference for the remainder payment allocation
                    $this->logPayment($loan, $data); // Apply remainder to the next installment
                }

            } else {
                // 2. Payment is PARTIAL
                // Note: Partial payments introduce complexity. For simplicity here, we assume only
                // full payments adjust the amortization schedule, but you must track what was paid.
                
                $newStatus = ($paymentAmount > 0) ? 'partial' : 'pending';

                $installment->update([
                    'status' => $newStatus,
                    // If complex tracking is needed, update a 'paid_amount' column here.
                ]);

                throw new Exception(
                    "Payment of UGX " . number_format($paymentAmount, 2) . " is only partial. " .
                    "UGX " . number_format($amountDue, 2) . " was due. Installment marked as PARTIAL."
                );
            }
            
            // Check if all installments are now paid
            $unpaidCount = $loan->installments()->whereIn('status', ['pending', 'partial', 'defaulted'])->count();
            if ($unpaidCount === 0) {
                $loan->update(['status' => 'completed']);
            }
            
            // Return the updated loan instance
            return $loan->refresh();

        });
    }
}