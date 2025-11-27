<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory;
    protected $fillable = [
        'transfer_id',
        'from_account',
        'to_account',
        'amount',
        'from_balance_before',
        'from_balance_after',
        'to_balance_before',
        'to_balance_after',
        'transferred_by',
        'reason'
    ];

    // Relation to user who made the transfer
    public function user()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

     // If you ever want to attach a specific sacco account record
    public function saccoAccount()
    {
        return $this->belongsTo(SaccoAccount::class);
    }
}
