<?php
use App\Models\Loan;

if (!function_exists('generateLoanId')) {
    function generateLoanId()
    {
        do {
            $randomNumber = '135' . mt_rand(100000000, 999999999); 
        } while (Loan::where('id', $randomNumber)->exists());

        return $randomNumber;
    
    }
}


