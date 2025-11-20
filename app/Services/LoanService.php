<?php

namespace App\Services;

use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function create(array $data): Loan
    {
        return DB::transaction(function () use ($data) {



            $principal = $data['principal_amount'];
            $rate = $data['interest_rate'];
            $duration = $data['duration_months'];

              $loanNumber = generateLoanId();

         

            // Calculate interest and totals
            $totalInterest = $this->calculateTotalInterest(
                $principal,
                $rate,
                $duration,
                $data['interest_type']
            );

            $totalAmountDue = $principal + $totalInterest;
            $monthlyRepayment = $duration > 0
                ? round($totalAmountDue / $duration, 2)
                : 0;

            $data['total_interest_due'] = $totalInterest;
            $data['total_amount_due'] = $totalAmountDue;
            $data['monthly_repayment_amount'] = $monthlyRepayment;
            $data['outstanding_balance'] = $totalAmountDue;

              $data['total_amount_due'] = $totalAmountDue;
            $data['outstanding_balance'] = $totalAmountDue;

            $data['loan_number'] =  $loanNumber;


            
          


            return Loan::create($data);
        });
    }

    public function update(Loan $loan, array $data): Loan
    {
        return DB::transaction(function () use ($loan, $data) {

            // Prevent editing fields that must never change after disbursement
            if ($loan->status === 'disbursed' && isset($data['principal_amount'])) {
                throw ValidationException::withMessages([
                    'principal_amount' => 'Cannot change principal once disbursed.'
                ]);
            }

            if (isset($data['amount_paid_to_date'])) {
                $data['outstanding_balance'] = max(
                    0,
                    $loan->total_amount_due - $data['amount_paid_to_date']
                );
            }

            $loan->update($data);

            return $loan;
        });
    }

    private function calculateTotalInterest(float $principal, float $rate, int $months, string $type): float
    {
        if ($months <= 0) {
            return 0;
        }

        if ($type === 'flat') {
            return round(($principal * ($rate / 100)) * $months, 2);
        }

        if ($type === 'reducing_balance') {
            $monthlyRate = $rate / 100;
            $balance = $principal;
            $interest = 0;

            for ($i = 0; $i < $months; $i++) {
                $monthInterest = $balance * $monthlyRate;
                $interest += $monthInterest;
                $balance -= ($principal / $months);
            }

            return round($interest, 2);
        }

        return 0;
    }
}
