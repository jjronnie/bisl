<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'payroll_grade_id',
        'employee_number',
        'employment_type',
        'qualification_level',
        'recognition_level',
        'meeting_allowance_eligible',
        'employment_start_date',
        'employment_end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'meeting_allowance_eligible' => 'boolean',
            'is_active' => 'boolean',
            'employment_start_date' => 'date',
            'employment_end_date' => 'date',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (PayrollProfile $profile) {
            if (empty($profile->employee_number)) {
                $profile->employee_number = static::generateEmployeeNumber();
            }
        });

        static::created(function (PayrollProfile $profile) {
            $profile->member->salaryAccount()->firstOrCreate([
                'member_id' => $profile->member_id,
            ]);
        });
    }

    protected static function generateEmployeeNumber(): string
    {
        do {
            $number = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (static::where('employee_number', $number)->exists());

        return $number;
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function payrollGrade(): BelongsTo
    {
        return $this->belongsTo(PayrollGrade::class);
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

    public function allowances(): HasMany
    {
        return $this->hasMany(PayrollAllowance::class);
    }
}
