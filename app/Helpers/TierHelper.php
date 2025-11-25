<?php

namespace App\Helpers;

use App\Models\Member;

class TierHelper
{
    /**
     * Update member tier based on savings account balance
     */
    public static function updateTier(Member $member): void
    {
        $balance = $member->savingsAccount->balance ?? 0;

        if ($balance >= 1000000) {
            $tier = 'gold';
        } else {
            $tier = 'silver';
        }

        if ($member->tier !== $tier) {
            $member->tier = $tier;
            $member->saveQuietly(); 
        }
    }
}
