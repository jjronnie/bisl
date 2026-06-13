<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayableAccount extends Model
{
    use HasFactory;
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
        return static::firstOrCreate(
            ['type' => 'tax'],
            ['balance' => 0, 'total_credited' => 0, 'total_withdrawn' => 0],
        );
    }

    public static function nssf(): self
    {
        return static::firstOrCreate(
            ['type' => 'nssf'],
            ['balance' => 0, 'total_credited' => 0, 'total_withdrawn' => 0],
        );
    }

    public function credit(float $amount): void
    {
        $this->increment('balance', $amount);
        $this->increment('total_credited', $amount);
    }
}
