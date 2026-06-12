<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollAttendance extends Model
{
    use HasFactory;

    protected $table = 'payroll_attendance';

    protected $fillable = [
        'payroll_profile_id',
        'payroll_period_id',
        'days_worked',
        'advance_amount',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'advance_amount' => 'decimal:2',
        ];
    }

    public function payrollProfile(): BelongsTo
    {
        return $this->belongsTo(PayrollProfile::class);
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }
}
