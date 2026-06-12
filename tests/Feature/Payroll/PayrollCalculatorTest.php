<?php

use App\Models\AllowanceType;
use App\Models\Member;
use App\Models\PayrollAttendance;
use App\Models\PayrollGrade;
use App\Models\PayrollMeetingAttendance;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use App\Models\PayrollTaxBracket;
use App\Services\PayrollCalculatorService;

beforeEach(function () {
    $this->calculator = app(PayrollCalculatorService::class);

    PayrollTaxBracket::factory()->create(['from_amount' => 0, 'to_amount' => 235000, 'rate' => 0, 'effective_from' => '2020-01-01']);
    PayrollTaxBracket::factory()->create(['from_amount' => 235000, 'to_amount' => 335000, 'rate' => 10, 'effective_from' => '2020-01-01']);
    PayrollTaxBracket::factory()->create(['from_amount' => 335000, 'to_amount' => 410000, 'rate' => 20, 'effective_from' => '2020-01-01']);
    PayrollTaxBracket::factory()->create(['from_amount' => 410000, 'to_amount' => null, 'rate' => 30, 'effective_from' => '2020-01-01']);

    AllowanceType::factory()->create(['code' => 'certificate', 'amount' => 20000]);
    AllowanceType::factory()->create(['code' => 'diploma', 'amount' => 40000]);
    AllowanceType::factory()->create(['code' => 'bachelors', 'amount' => 60000]);
    AllowanceType::factory()->create(['code' => 'masters', 'amount' => 80000]);
    AllowanceType::factory()->create(['code' => 'phd', 'amount' => 100000]);
    AllowanceType::factory()->create(['code' => 'appreciation', 'amount' => 10000]);
    AllowanceType::factory()->create(['code' => 'golden_medal', 'amount' => 20000]);
    AllowanceType::factory()->create(['code' => 'meeting', 'amount' => 5000]);
});

it('calculates full payroll correctly', function () {
    $grade = PayrollGrade::factory()->create([
        'monthly_basic_salary' => 1000000,
        'working_days_divisor' => 30,
    ]);

    $member = Member::factory()->create();
    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
        'qualification_level' => 'bachelors',
        'recognition_level' => 'appreciation',
        'meeting_allowance_eligible' => true,
    ]);

    $period = PayrollPeriod::factory()->create();

    $attendance = PayrollAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'days_worked' => 30,
    ]);

    $meetingAttendance = PayrollMeetingAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'meetings_attended' => 2,
    ]);

    $result = $this->calculator->calculate($profile, $attendance, $meetingAttendance);

    expect($result['daily_rate'])->toBe(round(1000000 / 30, 2));
    expect($result['basic_salary_earned'])->toBe(1000000.0);
    expect($result['qualification_allowance'])->toBe(60000.0);
    expect($result['recognition_allowance'])->toBe(10000.0);
    expect($result['meeting_allowance'])->toBe(10000.0);
    expect($result['meeting_count'])->toBe(2);
    expect($result['gross_salary'])->toBe((float) (1000000 + 60000 + 10000 + 10000));
    expect($result['nssf_employee'])->toBe(50000.0);
    expect($result['taxable_income'])->toBe((float) (1000000 - 50000));

    $expectedPaye = (335000 - 235000) * 0.10 + (410000 - 335000) * 0.20 + (950000 - 410000) * 0.30;
    expect($result['paye'])->toBe(round($expectedPaye, 2));

    $expectedDeductions = $result['paye'] + 50000 + 0;
    expect($result['total_deductions'])->toBe($expectedDeductions);

    $expectedNet = $result['gross_salary'] - $expectedDeductions;
    expect($result['net_salary'])->toBe($expectedNet);

    expect($result['savings_contribution'])->toBe(round($expectedNet * 0.05, 2));

    expect($result['final_take_home'])->toBe($result['net_salary'] - $result['savings_contribution']);
});

it('calculates payroll without meeting attendance', function () {
    $grade = PayrollGrade::factory()->create([
        'monthly_basic_salary' => 500000,
        'working_days_divisor' => 30,
    ]);

    $member = Member::factory()->create();
    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
        'meeting_allowance_eligible' => true,
    ]);

    $period = PayrollPeriod::factory()->create();

    $attendance = PayrollAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'days_worked' => 20,
    ]);

    $result = $this->calculator->calculate($profile, $attendance);

    $expectedEarned = round(500000 / 30 * 20, 2);
    expect($result['basic_salary_earned'])->toBe($expectedEarned);
    expect($result['meeting_allowance'])->toBe(0);
    expect($result['meeting_count'])->toBe(0);
});

it('calculates for partial month', function () {
    $grade = PayrollGrade::factory()->create([
        'monthly_basic_salary' => 900000,
        'working_days_divisor' => 30,
    ]);

    $member = Member::factory()->create();
    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
    ]);

    $period = PayrollPeriod::factory()->create();

    $attendance = PayrollAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'days_worked' => 15,
    ]);

    $result = $this->calculator->calculate($profile, $attendance);

    $expectedEarned = round(900000 / 30 * 15, 2);
    expect($result['basic_salary_earned'])->toBe($expectedEarned);
    expect($result['daily_rate'])->toBe(round(900000 / 30, 2));
});
