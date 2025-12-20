<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LoanDocument extends Model
{
    protected $fillable = [
        'loan_id',
        'name',
        'notes',
        'file_path',
    ];

     public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    // Helper to get full URL
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
