<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_profile_id',
        'payroll_period_id',
        'days_worked',
        'daily_rate',
        'basic_salary_earned',
        'meeting_count',
        'meeting_allowance',
        'qualification_allowance',
        'recognition_allowance',
        'other_allowances',
        'gross_salary',
        'nssf_employee',
        'taxable_income',
        'paye',
        'lst',
        'total_deductions',
        'net_salary',
        'savings_contribution',
        'advance_amount',
        'final_take_home',
        'status',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'days_worked' => 'integer',
            'daily_rate' => 'decimal:2',
            'basic_salary_earned' => 'decimal:2',
            'meeting_count' => 'integer',
            'meeting_allowance' => 'decimal:2',
            'qualification_allowance' => 'decimal:2',
            'recognition_allowance' => 'decimal:2',
            'other_allowances' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'nssf_employee' => 'decimal:2',
            'taxable_income' => 'decimal:2',
            'paye' => 'decimal:2',
            'lst' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'savings_contribution' => 'decimal:2',
            'advance_amount' => 'decimal:2',
            'final_take_home' => 'decimal:2',
            'generated_at' => 'datetime',
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

    public function member()
    {
        return $this->hasOneThrough(Member::class, PayrollProfile::class, 'id', 'id', 'payroll_profile_id', 'member_id');
    }

    public function payrollTransactions(): HasMany
    {
        return $this->hasMany(PayrollTransaction::class);
    }

    public function savingsContribution()
    {
        return $this->hasOne(PayrollSavingsContribution::class);
    }
}
