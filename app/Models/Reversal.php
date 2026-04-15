<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reversal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'reversed_by',
        'member_id',
        'account',
        'amount',
        'reason',
    ];

    /**
     * Get the transaction that was reversed.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the user who reversed the transaction.
     */
    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    /**
     * Get the member associated with the transaction.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
