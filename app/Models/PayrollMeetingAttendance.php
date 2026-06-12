<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollMeetingAttendance extends Model
{
    use HasFactory;

    protected $table = 'payroll_meeting_attendance';

    protected $fillable = [
        'payroll_profile_id',
        'payroll_period_id',
        'meetings_attended',
        'created_by',
    ];

    public function payrollProfile(): BelongsTo
    {
        return $this->belongsTo(PayrollProfile::class);
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }
}
