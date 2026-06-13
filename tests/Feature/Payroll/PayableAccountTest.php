<?php

use App\Models\PayableAccount;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $role = Role::firstOrCreate(['name' => 'superadmin']);

    $this->user = User::factory()->create();
    $this->user->assignRole('superadmin');
});

it('creates a tax account on first call if none exists', function () {
    PayableAccount::where('type', 'tax')->delete();

    $account = PayableAccount::tax();

    expect($account->type)->toBe('tax');
    expect($account->balance)->toBe('0.00');
});

it('creates an nssf account on first call if none exists', function () {
    PayableAccount::where('type', 'nssf')->delete();

    $account = PayableAccount::nssf();

    expect($account->type)->toBe('nssf');
    expect($account->balance)->toBe('0.00');
});

it('returns existing tax account without creating a duplicate', function () {
    $account = PayableAccount::tax();

    expect(PayableAccount::where('type', 'tax')->count())->toBe(1);
    expect($account->type)->toBe('tax');
});

it('returns existing nssf account without creating a duplicate', function () {
    $account = PayableAccount::nssf();

    expect(PayableAccount::where('type', 'nssf')->count())->toBe(1);
    expect($account->type)->toBe('nssf');
});

it('loads the tax ledger page', function () {
    PayableAccount::tax();

    $response = $this->actingAs($this->user)
        ->get(route('admin.payroll.ledgers.tax'));

    $response->assertOk();
});

it('loads the nssf ledger page', function () {
    PayableAccount::nssf();

    $response = $this->actingAs($this->user)
        ->get(route('admin.payroll.ledgers.nssf'));

    $response->assertOk();
});
