<?php

use App\Models\AllowanceType;
use App\Models\Member;
use App\Models\PayrollAttendance;
use App\Models\PayrollGrade;
use App\Models\PayrollPeriod;
use App\Models\PayrollProfile;
use App\Models\PayrollTaxBracket;
use App\Models\SaccoAccount;
use App\Services\PayrollPostingService;
use App\Services\SavingsContributionService;

beforeEach(function () {
    SaccoAccount::factory()->create();

    $this->savingsService = app(SavingsContributionService::class);

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

it('calculates 5 percent of net salary as savings contribution', function () {
    $contribution = $this->savingsService->calculateContribution(500000);

    expect($contribution)->toBe(25000.0);
});

it('creates transaction via transaction service on deposit', function () {
    $grade = PayrollGrade::factory()->create(['monthly_basic_salary' => 1000000]);
    $member = Member::factory()->create();

    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
    ]);

    $period = PayrollPeriod::factory()->create();

    $attendance = PayrollAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'days_worked' => 30,
    ]);

    $postingService = app(PayrollPostingService::class);
    $run = $postingService->post($profile, $period, $attendance, null);

    expect($run->savingsContribution)->not->toBeNull();
    expect($run->savingsContribution->amount)->toBe($run->savings_contribution);

    $member->refresh();
    expect($member->savingsAccount->balance)->toBe($run->savings_contribution);

    $savingsTxn = $member->transactions()->where('account', 'savings')->latest()->first();
    expect($savingsTxn)->not->toBeNull();
    expect($savingsTxn->amount)->toBe($run->savings_contribution);
    expect($savingsTxn->transaction_type)->toBe('deposit');
    expect($savingsTxn->account)->toBe('savings');
    expect($savingsTxn->method)->toBe('payroll');

    $salaryTxn = $member->transactions()->where('account', 'salary')->latest()->first();
    expect($salaryTxn)->not->toBeNull();
    expect($salaryTxn->amount)->toBe($run->final_take_home);
    expect($salaryTxn->transaction_type)->toBe('deposit');
    expect($salaryTxn->account)->toBe('salary');

    $member->refresh();
    expect((float) $member->salaryAccount->balance)->toBe((float) $run->final_take_home);
});

it('uses existing transaction service for balance updates', function () {
    $grade = PayrollGrade::factory()->create(['monthly_basic_salary' => 1000000]);
    $member = Member::factory()->create();
    $initialBalance = $member->savingsAccount->balance;

    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
    ]);

    $period = PayrollPeriod::factory()->create();

    $attendance = PayrollAttendance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'payroll_period_id' => $period->id,
        'days_worked' => 30,
    ]);

    $postingService = app(PayrollPostingService::class);
    $run = $postingService->post($profile, $period, $attendance, null);

    $member->refresh();
    expect((float) $member->savingsAccount->balance)->toBe($initialBalance + $run->savings_contribution);
});
