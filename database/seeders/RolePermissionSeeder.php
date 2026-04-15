<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $superAdmin = User::updateOrCreate(
            ['email' => 'ronaldjjuuko7@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('88928892'),
                'email_verified_at' => now(),
                'must_change_password' => false,
                'status' => 'active',
            ]
        );

        $superAdmin->syncRoles(['superadmin']);
    }
}
