<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanReminder extends Model
{
    use HasFactory;

    protected $table = 'loan_reminders';

    protected $fillable = [
        'loan_id',
        'installment_id',
        'type',
        'channel',
        'sent_at',
        'status',
        'message',
        'retry_count',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(LoanInstallment::class);
    }
}
