<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory;
    protected $fillable = [
    'member_id',
    'loan_number',

    'principal_amount',
    'interest_rate',
    'interest_type',
    'duration_months',

    'total_interest_due',
    'total_amount_due',
    'monthly_repayment_amount',

    'amount_paid_to_date',
    'outstanding_balance',

    'penalty_rate',
    'status',
    'purpose',

    'application_date',
    'approval_date',
    'disbursement_date',
    'due_date',

    'created_by',
    'approved_by',

    'notes'
];

}
