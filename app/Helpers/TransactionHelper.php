<?php
use App\Models\Transaction;

if (!function_exists('generateUniqueTransactionId')) {
    function generateUniqueTransactionId()
    {
        do {
            $randomNumber = '431' . mt_rand(100000000, 999999999); 
        } while (Transaction::where('id', $randomNumber)->exists());

        return $randomNumber;
    
    }
}


