<?php

use App\Mail\PenaltyApplied;
use App\Models\Member;
use App\Models\Penalty;
use App\Models\SaccoAccount;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $role = Role::findOrCreate('admin');
    $this->admin = User::factory()->create();
    $this->admin->assignRole($role);

    $this->actingAs($this->admin);

    SaccoAccount::factory()->create();

    Mail::fake();
});

it('applies late meeting penalty with meeting date and notes', function () {
    $member = Member::factory()->create();
    $savings = $member->savingsAccount;
    $savings->update(['balance' => 50000]);

    $response = $this->post(route('admin.members.apply-penalty', $member), [
        'type' => 'late_meeting',
        'meeting_date' => '2026-06-10',
        'notes' => 'Missed the monthly general meeting',
    ]);

    $response->assertSessionHas('success');

    $savings->refresh();
    expect($savings->balance)->toEqual(45000.00);

    $sacco = SaccoAccount::first();
    expect($sacco->operational)->toEqual(5000.00);

    expect(Transaction::where('member_id', $member->id)->count())->toBe(1);
    $transaction = Transaction::where('member_id', $member->id)->first();
    expect($transaction->amount)->toEqual(5000.00);
    expect($transaction->transaction_type)->toBe('withdrawal');
    expect($transaction->account)->toBe('savings');
    expect($transaction->side)->toBe('debit');
    expect($transaction->balance_before)->toEqual(50000.00);
    expect($transaction->balance_after)->toEqual(45000.00);
    expect($transaction->method)->toBe('penalty');
    expect($transaction->remarks)->toBe('Late Meeting Penalty');

    expect(Penalty::count())->toBe(1);
    $penalty = Penalty::first();
    expect($penalty->member_id)->toBe($member->id);
    expect($penalty->amount)->toEqual(5000.00);
    expect($penalty->type)->toBe('late_meeting');
    expect($penalty->meeting_date->format('Y-m-d'))->toBe('2026-06-10');
    expect($penalty->notes)->toBe('Missed the monthly general meeting');
    expect($penalty->balance_before)->toEqual(50000.00);
    expect($penalty->balance_after)->toEqual(45000.00);
    expect($penalty->applied_by)->toBe($this->admin->id);

    Mail::assertQueued(PenaltyApplied::class, function ($mail) use ($member, $penalty) {
        return $mail->penalty->id === $penalty->id
            && $mail->hasTo($member->user->email);
    });
});

it('applies loss of identity card penalty', function () {
    $member = Member::factory()->create();
    $savings = $member->savingsAccount;
    $savings->update(['balance' => 100000]);

    $response = $this->post(route('admin.members.apply-penalty', $member), [
        'type' => 'loss_identity_card',
    ]);

    $response->assertSessionHas('success');

    $savings->refresh();
    expect($savings->balance)->toEqual(65000.00);

    $sacco = SaccoAccount::first();
    expect($sacco->operational)->toEqual(35000.00);

    $penalty = Penalty::first();
    expect($penalty->amount)->toEqual(35000.00);
    expect($penalty->type)->toBe('loss_identity_card');
    expect($penalty->meeting_date)->toBeNull();
    expect($penalty->notes)->toBeNull();
    expect($penalty->applied_by)->toBe($this->admin->id);

    Mail::assertQueued(PenaltyApplied::class);
});

it('fails with insufficient balance', function () {
    $member = Member::factory()->create();
    $savings = $member->savingsAccount;
    $savings->update(['balance' => 2000]);

    $response = $this->post(route('admin.members.apply-penalty', $member), [
        'type' => 'late_meeting',
    ]);

    $response->assertSessionHas('error');

    $savings->refresh();
    expect((float) $savings->balance)->toEqual(2000.00);

    expect(Penalty::count())->toBe(0);
    expect(Transaction::count())->toBe(0);

    Mail::assertNotQueued(PenaltyApplied::class);
});

it('fails with invalid penalty type', function () {
    $member = Member::factory()->create();

    $response = $this->post(route('admin.members.apply-penalty', $member), [
        'type' => 'invalid_type',
    ]);

    $response->assertSessionHasErrors('type');
});

it('requires authentication to apply penalty', function () {
    $member = Member::factory()->create();

    $response = $this->post(route('admin.members.apply-penalty', $member), [
        'type' => 'late_meeting',
    ]);

    $response->assertRedirect();
});
