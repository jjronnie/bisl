<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaccoAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'operational',
        'loan_interest',
        'member_interest',
        'member_savings',
        'loan_protection_fund',
    ];
}
