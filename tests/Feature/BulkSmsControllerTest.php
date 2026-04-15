<?php

use App\Models\BulkSmsCampaign;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function createAdminUser(): User
{
    $role = Role::findOrCreate('admin');
    $admin = User::factory()->create();
    $admin->assignRole($role);

    return $admin;
}

test('bulk sms index page is displayed with campaigns list', function () {
    $admin = createAdminUser();
    BulkSmsCampaign::factory()->count(3)->create(['created_by' => $admin->id]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.bulk-sms.index'));

    $response->assertOk();
    $response->assertSee('Bulk SMS Campaigns');
    $response->assertSee('New Campaign');
});

test('bulk sms create page is displayed', function () {
    $admin = createAdminUser();
    Member::factory()->count(3)->create();

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.bulk-sms.create'));

    $response->assertOk();
    $response->assertSee('Select Recipients');
    $response->assertSee('Compose Message');
});

test('bulk sms show page displays campaign details', function () {
    $admin = createAdminUser();
    $campaign = BulkSmsCampaign::factory()->create([
        'created_by' => $admin->id,
        'message' => 'Test message',
        'total_recipients' => 5,
        'sent_count' => 3,
        'failed_count' => 2,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.bulk-sms.show', $campaign->id));

    $response->assertOk();
    $response->assertSee('Test message');
    $response->assertSee('5');
    $response->assertSee('3');
    $response->assertSee('2');
});

test('bulk sms status endpoint returns json', function () {
    $admin = createAdminUser();
    $campaign = BulkSmsCampaign::factory()->create([
        'created_by' => $admin->id,
        'status' => 'processing',
        'total_recipients' => 10,
        'sent_count' => 5,
        'failed_count' => 1,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.bulk-sms.status', $campaign->id));

    $response->assertOk();
    $response->assertJsonStructure([
        'id',
        'status',
        'total_recipients',
        'sent_count',
        'failed_count',
        'total_cost',
        'progress',
    ]);
});

test('bulk sms cancel returns json success', function () {
    $admin = createAdminUser();
    $campaign = BulkSmsCampaign::factory()->create([
        'created_by' => $admin->id,
        'status' => 'processing',
    ]);

    $response = $this
        ->actingAs($admin)
        ->withoutMiddleware(PreventRequestForgery::class)
        ->post(route('admin.bulk-sms.cancel', $campaign->id));

    $response->assertJson(['success' => true]);
    $campaign->refresh();
    expect($campaign->status)->toBe('cancelled');
});

test('cannot cancel completed campaign', function () {
    $admin = createAdminUser();
    $campaign = BulkSmsCampaign::factory()->create([
        'created_by' => $admin->id,
        'status' => 'completed',
    ]);

    $response = $this
        ->actingAs($admin)
        ->withoutMiddleware(PreventRequestForgery::class)
        ->post(route('admin.bulk-sms.cancel', $campaign->id));

    $response->assertJson(['success' => false]);
});
