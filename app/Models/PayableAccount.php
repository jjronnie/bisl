<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayableAccount extends Model
{
    protected $fillable = [
        'type',
        'balance',
        'total_credited',
        'total_withdrawn',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'total_credited' => 'decimal:2',
            'total_withdrawn' => 'decimal:2',
        ];
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(PayableWithdrawal::class);
    }

    public static function tax(): self
    {
        return static::where('type', 'tax')->firstOrFail();
    }

    public static function nssf(): self
    {
        return static::where('type', 'nssf')->firstOrFail();
    }

    public function credit(float $amount): void
    {
        $this->increment('balance', $amount);
        $this->increment('total_credited', $amount);
    }
}
