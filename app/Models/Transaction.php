<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
   protected $fillable = [
        'member_id',
        'reference_number',
        'loan_id',
        'creator',
        'transaction_type',
        'side',
        'amount',
        'balance_before',
        'balance_after',
        'method',
        'remarks',
        'transaction_date'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
  

    // --- RELATIONSHIPS ---

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function savingsAccount(): BelongsTo
    {
        return $this->belongsTo(SavingsAccount::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }


public function createdBy()
{
    return $this->belongsTo(User::class, 'creator');
}

    
}