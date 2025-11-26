<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanInstallment;
use Illuminate\Support\Facades\DB;
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
                throw new Exception("No outstanding installments found. Loan #{$loan->loan_number} is already paid off.");
            }

            // Calculate the total amount due for this installment (Principal + Interest + Penalty)
            $amountDue = $installment->principal_amount + $installment->interest_amount + $installment->penalty_amount;
            
            // If the installment was partial, determine the remaining due amount
            // NOTE: In a more complex system, 'paid_amount' would track payments.
            // For simplicity here, we assume if status is 'partial', the full amount due is needed
            // unless we track the exact partial payment amount in the installment table itself.
            // We'll proceed assuming 'amountDue' is the full required amount.

            if ($paymentAmount >= $amountDue) {
                // 1. Payment is FULL or OVER
                $installment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    // Optionally update the loan's status if this was the final installment
                ]);

                // Recursive call or loop to handle overpayment against the NEXT installment
                $remainingPayment = $paymentAmount - $amountDue;
                if ($remainingPayment > 0) {
                    // Recalculate data for recursion (use the remainder as the new payment amount)
                    $data['payment_amount'] = $remainingPayment;
                    $this->logPayment($loan, $data); // Apply remainder to the next installment
                }

            } else {
                // 2. Payment is PARTIAL
                $newStatus = ($paymentAmount > 0) ? 'partial' : 'pending';

                $installment->update([
                    'status' => $newStatus,
                    // In a simple model, we just log the transaction externally. 
                    // In a complex model, we would update a 'paid_amount' column here.
                    // For this simple example, we'll mark it as 'partial' and rely on the next payment to cover the rest.
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

    // A separate method could handle payment creation/transaction logging if needed
    // ...
}