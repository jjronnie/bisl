<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;
    
    protected $fillable = [
    'member_id',
    'created_by',
    'rejected_by',
    'approved_by',
    'loan_number',
    'loan_type',
    'status',           // e.g., pending, approved, defaulted
    'amount',
    'interest_rate',
    'duration_months',  // Matches the DB column, not the input name 'period'
    'application_date',
    'approval_date',
    'disbursement_date',
    'due_date',
    'purpose',
    'notes',
];

    protected $guarded = ['id'];

    protected $casts = [
        'application_date' => 'date',
        'approval_date' => 'date',
        'disbursement_date' => 'date',
        'due_date' => 'date',
    ];



    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }



    public function installments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class)->orderBy('installment_number');
    }
}
