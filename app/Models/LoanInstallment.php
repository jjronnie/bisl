<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanInstallment extends Model
{
      use HasFactory;

    protected $fillable = [
    'loan_id',
    'installment_number',
    'due_date',
    'starting_balance',
    'principal_amount', // The portion reducing the loan
    'interest_amount',  // The profit portion
    'total_amount',     // The EMI (Principal + Interest)
    'ending_balance',
    'penalty_amount',
    'paid_at',
    'status',           // e.g., pending, paid, partial
];


    protected $guarded = ['id'];
    
    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}