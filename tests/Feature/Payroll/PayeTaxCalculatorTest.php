<?php

use App\Models\PayrollTaxBracket;
use App\Services\TaxCalculatorService;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $this->calculator = app(TaxCalculatorService::class);

    PayrollTaxBracket::factory()->create([
        'from_amount' => 0,
        'to_amount' => 235000,
        'rate' => 0,
        'effective_from' => '2020-01-01',
        'effective_to' => null,
    ]);

    PayrollTaxBracket::factory()->create([
        'from_amount' => 235000,
        'to_amount' => 335000,
        'rate' => 10,
        'effective_from' => '2020-01-01',
        'effective_to' => null,
    ]);

    PayrollTaxBracket::factory()->create([
        'from_amount' => 335000,
        'to_amount' => 410000,
        'rate' => 20,
        'effective_from' => '2020-01-01',
        'effective_to' => null,
    ]);

    PayrollTaxBracket::factory()->create([
        'from_amount' => 410000,
        'to_amount' => null,
        'rate' => 30,
        'effective_from' => '2020-01-01',
        'effective_to' => null,
    ]);
});

it('calculates taxable income correctly', function () {
    $taxable = $this->calculator->calculateTaxableIncome(500000, 25000);

    expect($taxable)->toBe(475000.0);
});

it('taxable income is never negative', function () {
    $taxable = $this->calculator->calculateTaxableIncome(10000, 50000);

    expect($taxable)->toBe(0.0);
});

it('returns zero paye when income is below first bracket', function () {
    $result = $this->calculator->calculatePaye(100000);

    expect($result['paye'])->toBe(0.0);
    expect($result['bracket'])->toBe('0%');
});

it('applies 10 percent bracket correctly', function () {
    $result = $this->calculator->calculatePaye(300000);

    $expectedPaye = (300000 - 235000) * 0.10;

    expect($result['paye'])->toBe(round($expectedPaye, 2));
});

it('applies 20 percent bracket correctly', function () {
    $result = $this->calculator->calculatePaye(400000);

    $bracket10 = (335000 - 235000) * 0.10;
    $bracket20 = (400000 - 335000) * 0.20;
    $expectedPaye = $bracket10 + $bracket20;

    expect($result['paye'])->toBe(round($expectedPaye, 2));
});

it('applies 30 percent bracket correctly', function () {
    $result = $this->calculator->calculatePaye(500000);

    $bracket10 = (335000 - 235000) * 0.10;
    $bracket20 = (410000 - 335000) * 0.20;
    $bracket30 = (500000 - 410000) * 0.30;
    $expectedPaye = $bracket10 + $bracket20 + $bracket30;

    expect($result['paye'])->toBe(round($expectedPaye, 2));
});

it('reads brackets from database not hardcoded', function () {
    PayrollTaxBracket::where('rate', 30)->update(['rate' => 25]);

    app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

    $this->calculator = app(TaxCalculatorService::class);

    $result = $this->calculator->calculatePaye(500000);

    $bracket10 = (335000 - 235000) * 0.10;
    $bracket20 = (410000 - 335000) * 0.20;
    $bracket30 = (500000 - 410000) * 0.25;

    expect($result['paye'])->toBe(round($bracket10 + $bracket20 + $bracket30, 2));
});

it('handles no tax brackets gracefully', function () {
    PayrollTaxBracket::query()->delete();

    $result = $this->calculator->calculatePaye(500000);

    expect($result['paye'])->toBe(0.0);
    expect($result['bracket'])->toBe('none');
});

it('handles very large income correctly', function () {
    $result = $this->calculator->calculatePaye(10000000);

    expect($result['paye'])->toBeGreaterThan(0);
});
