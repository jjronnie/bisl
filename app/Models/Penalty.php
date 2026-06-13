<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    protected $fillable = [
        'member_id',
        'amount',
        'type',
        'meeting_date',
        'notes',
        'balance_before',
        'balance_after',
        'applied_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }
}
