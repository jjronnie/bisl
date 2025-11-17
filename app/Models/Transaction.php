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
        'reference_id',
        'member_id',
        'savings_account_id',
        'loan_id',
        'transacted_by_user_id',
        'transaction_type',
        'method',
        'amount',
        'is_debit',
        'running_balance',
        'description',
        'remarks',
        'transaction_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'is_debit' => 'boolean',
        'transaction_date' => 'datetime',
    ];

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

    /**
     * Get the user (staff) who posted the transaction.
     */
    public function transactedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transacted_by_user_id');
    }
}