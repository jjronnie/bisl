<?php

namespace App\Services;

use App\Models\PayrollTaxBracket;

class TaxCalculatorService
{
    public function calculateTaxableIncome(float $earnedSalary, float $nssfDeduction): float
    {
        $taxable = $earnedSalary - $nssfDeduction;

        return max(0, $taxable);
    }

    public function calculatePaye(float $taxableIncome): array
    {
        $brackets = PayrollTaxBracket::where('effective_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->orderBy('from_amount')
            ->get();

        if ($brackets->isEmpty()) {
            return ['paye' => 0.0, 'bracket' => 'none'];
        }

        $paye = 0.0;
        $remaining = $taxableIncome;
        $appliedBracket = 'none';

        foreach ($brackets as $bracket) {
            if ($remaining <= 0) {
                break;
            }

            $bracketMax = $bracket->to_amount ?? PHP_FLOAT_MAX;
            $bracketSize = max(0, $bracketMax - $bracket->from_amount);
            $taxableInBracket = min($remaining, $bracketSize);

            $paye += round($taxableInBracket * ($bracket->rate / 100), 2);
            $remaining -= $taxableInBracket;

            $rateFormatted = $bracket->rate == (int) $bracket->rate
                ? (int) $bracket->rate
                : $bracket->rate;
            $appliedBracket = $rateFormatted.'%';
        }

        return [
            'paye' => round($paye, 2),
            'bracket' => $appliedBracket,
        ];
    }
}
