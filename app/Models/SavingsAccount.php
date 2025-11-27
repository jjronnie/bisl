<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'account_number',
        'balance',
        'loan_protection_fund',
        'membership_fee',
        'status',

    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transactions()
    {
        return $this->member ? $this->member->transactions() : collect();
    }




}
