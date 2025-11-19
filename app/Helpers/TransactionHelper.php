<?php
use App\Models\Transaction;

if (!function_exists('generateTransactionId')) {
    function generateTransactionId()
    {
        do {
            $randomNumber = '135' . mt_rand(100000000, 999999999); 
        } while (Transaction::where('id', $randomNumber)->exists());

        return $randomNumber;
    
    }
}


