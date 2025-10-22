<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',        // foreign key to members table
        'account_number',   // unique identifier for the account
        'balance',          // current balance
        'status',           // active, suspended, closed
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
