<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'monthly_basic_salary',
        'working_days_divisor',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'monthly_basic_salary' => 'decimal:2',
            'working_days_divisor' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function payrollProfiles(): HasMany
    {
        return $this->hasMany(PayrollProfile::class);
    }

    public function dailyRate(): float
    {
        if ($this->working_days_divisor <= 0) {
            return 0;
        }

        return $this->monthly_basic_salary / $this->working_days_divisor;
    }
}
