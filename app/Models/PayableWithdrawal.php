<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayableWithdrawal extends Model
{
    protected $fillable = [
        'payable_account_id',
        'amount',
        'reason',
        'withdrawn_by',
        'withdrawn_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'withdrawn_at' => 'datetime',
        ];
    }

    public function payableAccount(): BelongsTo
    {
        return $this->belongsTo(PayableAccount::class);
    }

    public function withdrawnBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'withdrawn_by');
    }
}
