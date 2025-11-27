<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterestLedger extends Model
{

    protected $table = 'interest_ledger';
     protected $fillable = [
        'member_id',
        'balance_before',
        'interest_amount',
        'balance_after',
        'tier',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
