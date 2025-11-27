<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanInstallment;
use App\Models\SaccoAccount;
use Illuminate\Support\Facades\DB;
use App\Helpers\LoanHelper; // Your helper for ID generation
use Carbon\Carbon;
use Exception;

class LoanService
{
    /**
     * Create a new Loan and its Amortization Schedule.
     */
    public function createLoan(array $data, int $creatorId): Loan
    {
        return DB::transaction(function () use ($data, $creatorId) {

            $member = Member::findOrFail($data['member_id']);

            // 1. Business Logic Validations
               $this->validateLoanEligibility($member, $data['loan_type'], $data['amount']);

            $reference = generateLoanId();

            // 2. Create Loan Record
            $loan = Loan::create([
                'member_id' => $member->id,
                'created_by' => $creatorId,
                'approved_by' => $creatorId,
                'rejected_by' => $creatorId,
                'loan_number' => $reference,
                'loan_type' => $data['loan_type'],
                'amount' => $data['amount'],
                'interest_rate' => $data['interest_rate'],
                'duration_months' => $data['period'], // mapped from 'period' input
                'purpose' => $data['purpose'],
                'application_date' => $data['application_date'],
                'status' => 'pending',

            ]);

            // 3. Generate Amortization Schedule
            $this->generateAmortizationSchedule($loan);

            return $loan;
        });
    }

    /**
     * Check if member can take a loan.
     */
    private function validateLoanEligibility(Member $member, string $loanType, float $requestedAmount): void
    {
        // Rule 1: Priority loans strictly for Gold tier
        if ($loanType === 'priority' && $member->tier !== 'gold') {
            throw new Exception("Priority loans are reserved for Gold Tier members only.");
        }

        // Rule 2: Check for existing active loans
        $activeStatuses = ['approved', 'disbursed', 'active', 'defaulted', 'default_pending'];

        $hasActiveLoan = $member->loans()
            ->whereIn('status', $activeStatuses)
            ->exists();

        if ($hasActiveLoan) {
            throw new Exception("Member has an existing active loan.");
        }

        
        $saccoAccount = SaccoAccount::first();

        if (!$saccoAccount) {
            throw new Exception("SACCO operational account not found. Cannot determine available funds.");
        }

        $availableFunds = $saccoAccount->member_savings; // Using the field name from your query

        if ($requestedAmount > $availableFunds) {
            throw new Exception("Requested loan amount (UGX " . number_format($requestedAmount, 2) . ") is greater than the available lending funds (UGX " . number_format($availableFunds, 2) . ").");
        }
    }
    /**
     * Calculate Reducing Balance Amortization and save installments.
     * Formula: EMI = [P x R x (1+R)^N]/[(1+R)^N-1]
     */
    private function generateAmortizationSchedule(Loan $loan): void
    {
        $principal = $loan->amount;
        $annualRate = $loan->interest_rate;
        $months = $loan->duration_months;

        // Convert annual rate to monthly decimal (e.g., 12% -> 0.12 / 12 = 0.01)
        $monthlyRate = ($annualRate / 12) / 100;

        // Calculate EMI (Equated Monthly Installment)
        // If rate is 0 (unlikely for SACCO but possible), handle division by zero
        if ($monthlyRate > 0) {
            $emi = ($principal * $monthlyRate * pow(1 + $monthlyRate, $months)) /
                (pow(1 + $monthlyRate, $months) - 1);
        } else {
            $emi = $principal / $months;
        }

        $balance = $principal;
        // Since loan is just applied, we estimate start date. 
        // NOTE: These dates usually reset when status changes to 'disbursed'.
        $paymentDate = Carbon::parse($loan->application_date)->addMonth();

        for ($i = 1; $i <= $months; $i++) {

            $interestForMonth = $balance * $monthlyRate;
            $principalForMonth = $emi - $interestForMonth;

            // Handle last month rounding differences
            if ($i == $months) {
                $principalForMonth = $balance;
                $emi = $principalForMonth + $interestForMonth;
            }

            $endingBalance = $balance - $principalForMonth;

            LoanInstallment::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => $paymentDate->copy(),
                'starting_balance' => round($balance, 2),
                'principal_amount' => round($principalForMonth, 2),
                'interest_amount' => round($interestForMonth, 2),
                'total_amount' => round($emi, 2), // The amount user pays
                'ending_balance' => round($endingBalance < 0 ? 0 : $endingBalance, 2),
                'status' => 'pending'
            ]);

            $balance = $endingBalance;
            $paymentDate->addMonth();
        }
    }


    public function approve(Loan $loan): Loan
    {
        if ($loan->status !== 'pending') {
            throw new Exception("Loan must be pending to be approved.");
        }

        $loan->update([
            'status' => 'approved',
            'approval_date' => now(),
        ]);

        return $loan;
    }

    /**
     * Rejects a loan.
     */
    public function reject(Loan $loan): Loan
    {
        if ($loan->status !== 'pending') {
            throw new Exception("Only pending loans can be rejected.");
        }

        $loan->update([
            'status' => 'rejected',
        ]);

        return $loan;
    }

    /**
     * Disburses a loan, updates the amortization schedule dates, and activates the loan.
     */
    public function disburse(Loan $loan): Loan
    {
        if ($loan->status !== 'approved') {
            throw new Exception("Loan must be approved before disbursement.");
        }

        $disbursementDate = now();
        $maturityDate = $disbursementDate->copy()->addMonths($loan->duration_months);
        $saccoAccount = SaccoAccount::first();

        return DB::transaction(function () use ($loan, $disbursementDate, $maturityDate, $saccoAccount) {

                if ($loan->amount > $saccoAccount->member_savings) {
                throw new Exception("Disbursement failed: Loan amount exceeds current available lending funds.");
            }

               $saccoAccount->member_savings -= $loan->amount;
            $saccoAccount->save();
            

            // 1. Update Loan Record
            $loan->update([
                'status' => 'active',
                'disbursement_date' => $disbursementDate,
                'due_date' => $maturityDate,
            ]);

            // 2. Adjust Installment Due Dates
            // The first payment is due one month after disbursement.
            $nextDueDate = $disbursementDate->copy()->addMonth();

            foreach ($loan->installments as $installment) {
                $installment->update([
                    'due_date' => $nextDueDate->copy(),
                ]);
                $nextDueDate->addMonth();
            }

            return $loan;
        });
    }


 
    /**
     * Checks for defaults and applies penalty to remaining installments.
     * This method would typically be called via a daily scheduled task (Laravel Command/Cron).
     */
    public function applyDefaultPenalty(Loan $loan): void
    {
        if ($loan->status !== 'active') {
            return; // Only apply penalty to active loans
        }

        $pendingInstallments = $loan->installments()
            ->where('status', 'pending')
            ->where('due_date', '<', now()->startOfDay())
            ->get();

        if ($pendingInstallments->isNotEmpty()) {

            // Update the loan status to indicate potential default
            if ($loan->status !== 'default_pending') {
                $loan->update(['status' => 'default_pending']);
            }

            // The penalty is UGX 5000 per remaining installment (including the defaulted one)
            $penaltyAmount = 5000.00;

            // Apply penalty to ALL remaining PENDING installments
            $loan->installments()
                ->whereIn('status', ['pending', 'partial'])
                ->increment('penalty_amount', $penaltyAmount);

            // Note: The next time a payment is logged, this increased penalty_amount must be collected.
        }
    }

    /**
     * Marks the loan as completed when the final installment is paid.
     */
    public function markCompleted(Loan $loan): Loan
    {
        // This check would be part of the payment processing logic
        $unpaidBalance = $loan->installments()
            ->whereIn('status', ['pending', 'partial'])
            ->sum(DB::raw('principal_amount + interest_amount + penalty_amount'));

        if ($unpaidBalance <= 0) {
            $loan->update(['status' => 'completed']);
        }

        return $loan;
    }

}