<?php

use App\Models\AllowanceType;
use App\Models\Member;
use App\Models\PayrollAllowance;
use App\Models\PayrollGrade;
use App\Models\PayrollProfile;
use App\Services\AllowanceCalculatorService;

beforeEach(function () {
    $this->calculator = app(AllowanceCalculatorService::class);
});

it('calculates certificate allowance', function () {
    AllowanceType::factory()->create(['code' => 'certificate', 'amount' => 20000]);

    $amount = $this->calculator->calculateQualificationAllowance('certificate');

    expect($amount)->toBe(20000.0);
});

it('calculates all qualification allowances', function () {
    AllowanceType::factory()->create(['code' => 'certificate', 'amount' => 20000]);
    AllowanceType::factory()->create(['code' => 'diploma', 'amount' => 40000]);
    AllowanceType::factory()->create(['code' => 'bachelors', 'amount' => 60000]);
    AllowanceType::factory()->create(['code' => 'masters', 'amount' => 80000]);
    AllowanceType::factory()->create(['code' => 'phd', 'amount' => 100000]);

    expect($this->calculator->calculateQualificationAllowance('certificate'))->toBe(20000.0);
    expect($this->calculator->calculateQualificationAllowance('diploma'))->toBe(40000.0);
    expect($this->calculator->calculateQualificationAllowance('bachelors'))->toBe(60000.0);
    expect($this->calculator->calculateQualificationAllowance('masters'))->toBe(80000.0);
    expect($this->calculator->calculateQualificationAllowance('phd'))->toBe(100000.0);
});

it('returns zero for unknown qualification level', function () {
    $amount = $this->calculator->calculateQualificationAllowance('unknown');

    expect($amount)->toBe(0.0);
});

it('calculates recognition allowances', function () {
    AllowanceType::factory()->create(['code' => 'appreciation', 'amount' => 10000]);
    AllowanceType::factory()->create(['code' => 'golden_medal', 'amount' => 20000]);

    expect($this->calculator->calculateRecognitionAllowance('none'))->toBe(0.0);
    expect($this->calculator->calculateRecognitionAllowance('appreciation'))->toBe(10000.0);
    expect($this->calculator->calculateRecognitionAllowance('golden_medal'))->toBe(20000.0);
});

it('calculates meeting allowance with configurable rate', function () {
    AllowanceType::factory()->create(['code' => 'meeting', 'amount' => 5000]);

    $amount = $this->calculator->calculateMeetingAllowance(3);

    expect($amount)->toBe(15000.0);
});

it('returns zero meeting allowance for zero meetings', function () {
    $amount = $this->calculator->calculateMeetingAllowance(0);

    expect($amount)->toBe(0.0);
});

it('calculates other allowances from profile', function () {
    $member = Member::factory()->create();
    $grade = PayrollGrade::factory()->create();
    $profile = PayrollProfile::factory()->create([
        'member_id' => $member->id,
        'payroll_grade_id' => $grade->id,
    ]);

    $allowanceType = AllowanceType::factory()->create(['amount' => 10000]);
    AllowanceType::factory()->create(['amount' => 15000]);

    PayrollAllowance::factory()->create([
        'payroll_profile_id' => $profile->id,
        'allowance_type_id' => $allowanceType->id,
        'amount' => 10000,
        'is_active' => true,
        'effective_from' => now()->subMonth(),
    ]);

    $amount = $this->calculator->calculateOtherAllowances($profile);

    expect($amount)->toBe(10000.0);
});
