<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllowanceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'amount',
        'is_taxable',
        'is_recurring',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_taxable' => 'boolean',
            'is_recurring' => 'boolean',
        ];
    }

    public function payrollAllowances(): HasMany
    {
        return $this->hasMany(PayrollAllowance::class);
    }
}
