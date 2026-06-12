<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    protected $fillable = [
        'savings_deduction_percentage',
    ];

    protected function casts(): array
    {
        return [
            'savings_deduction_percentage' => 'decimal:2',
        ];
    }

    public static function getInstance(): self
    {
        return self::firstOrCreate([], [
            'savings_deduction_percentage' => 5.00,
        ]);
    }

    public static function savingsRate(): float
    {
        return (float) self::getInstance()->savings_deduction_percentage / 100;
    }
}
