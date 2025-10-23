<?php
use App\Models\SavingsAccount;

if (!function_exists('generateAccountNumber')) {
    function generateAccountNumber()
    {
        do {
            $accountNumber = '10001' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (SavingsAccount::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }
}
