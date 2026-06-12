<?php

use App\Services\NssfCalculatorService;

beforeEach(function () {
    $this->calculator = app(NssfCalculatorService::class);
});

it('calculates employee nssf contribution at 5 percent', function () {
    $contribution = $this->calculator->calculateEmployeeContribution(500000);

    expect($contribution)->toBe(25000.0);
});

it('calculates employer nssf contribution at 10 percent', function () {
    $contribution = $this->calculator->calculateEmployerContribution(500000);

    expect($contribution)->toBe(50000.0);
});

it('returns zero nssf for zero salary', function () {
    $employee = $this->calculator->calculateEmployeeContribution(0);
    $employer = $this->calculator->calculateEmployerContribution(0);

    expect($employee)->toBe(0.0);
    expect($employer)->toBe(0.0);
});

it('rounds nssf contributions to 2 decimal places', function () {
    $contribution = $this->calculator->calculateEmployeeContribution(33333);

    expect($contribution)->toBe(round(33333 * 0.05, 2));
});
