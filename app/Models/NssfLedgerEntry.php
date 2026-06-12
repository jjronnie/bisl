<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NssfLedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_run_id',
        'employee_amount',
        'employer_amount',
        'total_amount',
        'status',
        'remitted_at',
    ];

    protected function casts(): array
    {
        return [
            'employee_amount' => 'decimal:2',
            'employer_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'remitted_at' => 'datetime',
        ];
    }

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }
}
