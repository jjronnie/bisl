<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'status',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'processed_at' => 'datetime',
        ];
    }

    public function payrollRuns(): HasMany
    {
        return $this->hasMany(PayrollRun::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(PayrollAttendance::class);
    }

    public function meetingAttendance(): HasMany
    {
        return $this->hasMany(PayrollMeetingAttendance::class);
    }
}
