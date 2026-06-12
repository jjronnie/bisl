<?php

use App\Models\AllowanceType;
use App\Models\Member;
use App\Models\PayableAccount;
use App\Models\PayrollAttendance;
use App\Models\PayrollGrade;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use App\Models\PayrollRun;
use App\Models\PayrollTaxBracket;
use App\Services\PayrollPostingService;

beforeEach(function () {
    $this->postingService = app(PayrollPostingService::class);

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

it('posts payroll successfully', function () {
    $grade = PayrollGrade::factory()->create(['monthly_basic_salary' => 1000000]);
    $member = Member::factory()->create();

    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
    ]);

    $period = PayrollPeriod::factory()->create(['status' => 'draft']);

    $attendance = PayrollAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'days_worked' => 30,
    ]);

    $run = $this->postingService->post($profile, $period, $attendance, null);

    expect($run)->toBeInstanceOf(PayrollRun::class);
    expect($run->status)->toBe('completed');
    expect($run->payroll_profile_id)->toBe($profile->id);
    expect($run->payroll_period_id)->toBe($period->id);
    expect($run->generated_at)->not->toBeNull();
    expect($run->payrollTransactions)->toHaveCount(6);
    expect($run->savingsContribution)->not->toBeNull();
    expect($run->savingsContribution->posted_to_savings)->toBeTrue();

    $member->refresh();
    expect($member->savingsAccount->balance)->toBeGreaterThan(0);

    $taxAccount = PayableAccount::tax();
    expect((float) $taxAccount->balance)->toBeGreaterThan(0);

    $nssfAccount = PayableAccount::nssf();
    expect((float) $nssfAccount->balance)->toBeGreaterThan(0);
});

it('prevents duplicate posting for same employee and period', function () {
    $grade = PayrollGrade::factory()->create(['monthly_basic_salary' => 1000000]);
    $member = Member::factory()->create();

    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
    ]);

    $period = PayrollPeriod::factory()->create(['status' => 'draft']);

    $attendance = PayrollAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'days_worked' => 30,
    ]);

    $this->postingService->post($profile, $period, $attendance, null);

    expect(fn () => $this->postingService->post($profile, $period, $attendance, null))
        ->toThrow(Exception::class, 'already completed');
});

it('credits tax payable account on dispatch', function () {
    $grade = PayrollGrade::factory()->create(['monthly_basic_salary' => 1000000]);
    $member = Member::factory()->create();

    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
    ]);

    $period = PayrollPeriod::factory()->create(['status' => 'draft']);

    $attendance = PayrollAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'days_worked' => 30,
    ]);

    $balanceBefore = (float) PayableAccount::tax()->balance;

    $run = $this->postingService->post($profile, $period, $attendance, null);

    $taxAccount = PayableAccount::tax();
    expect((float) $taxAccount->balance)->toBe($balanceBefore + (float) $run->paye);
});

it('credits nssf payable account on dispatch', function () {
    $grade = PayrollGrade::factory()->create(['monthly_basic_salary' => 1000000]);
    $member = Member::factory()->create();

    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
    ]);

    $period = PayrollPeriod::factory()->create(['status' => 'draft']);

    $attendance = PayrollAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'days_worked' => 30,
    ]);

    $balanceBefore = (float) PayableAccount::nssf()->balance;

    $run = $this->postingService->post($profile, $period, $attendance, null);

    $nssfAccount = PayableAccount::nssf();
    expect((float) $nssfAccount->balance)->toBe($balanceBefore + (float) $run->nssf_employee);
});

it('ensures payroll run is immutable after completion (no duplicate transactions)', function () {
    $grade = PayrollGrade::factory()->create(['monthly_basic_salary' => 1000000]);
    $member = Member::factory()->create();

    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
    ]);

    $period = PayrollPeriod::factory()->create(['status' => 'draft']);

    $attendance = PayrollAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'days_worked' => 30,
    ]);

    $run = $this->postingService->post($profile, $period, $attendance, null);

    expect(PayrollRun::where('payroll_profile_id', $profile->id)
        ->where('payroll_period_id', $period->id)
        ->count())->toBe(1);

    $initialTxnCount = $run->payrollTransactions()->count();
    $initialBalance = $member->fresh()->savingsAccount->balance;

    expect(fn () => $this->postingService->post($profile, $period, $attendance, null))
        ->toThrow(Exception::class);

    $member->refresh();
    expect($member->savingsAccount->balance)->toBe($initialBalance);
    expect($run->fresh()->payrollTransactions()->count())->toBe($initialTxnCount);
});
