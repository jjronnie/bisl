<?php

use App\Models\PayrollGrade;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $role = Role::firstOrCreate(['name' => 'superadmin']);

    $this->user = User::factory()->create();
    $this->user->assignRole('superadmin');
});

it('can show the grades index page', function () {
    PayrollGrade::factory()->create(['name' => 'Test Grade']);

    $response = $this->actingAs($this->user)
        ->get(route('admin.payroll.grades.index'));

    $response->assertOk();
    $response->assertSee('Test Grade');
});

it('can show the grade edit page with valid data', function () {
    $grade = PayrollGrade::factory()->create([
        'name' => 'Executive Office',
        'monthly_basic_salary' => 5000000,
        'working_days_divisor' => 26,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('admin.payroll.grades.edit', ['grade' => $grade->id]));

    $response->assertOk();
    $response->assertSee('Executive Office');
});

it('can update a grade', function () {
    $grade = PayrollGrade::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($this->user)
        ->patch(route('admin.payroll.grades.update', ['grade' => $grade->id]), [
            'name' => 'New Name',
            'monthly_basic_salary' => 3000000,
            'working_days_divisor' => 26,
        ]);

    $response->assertRedirect(route('admin.payroll.grades.index'));
    $this->assertDatabaseHas('payroll_grades', [
        'id' => $grade->id,
        'name' => 'New Name',
    ]);
});
