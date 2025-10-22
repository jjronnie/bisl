<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Roles
        $adminRole      = Role::firstOrCreate(['name' => 'admin']);
        $userRole       = Role::firstOrCreate(['name' => 'user']);
        
        // You can define permissions here if needed globally, 
        // but for now, we focus on roles.
        // Example: Permission::firstOrCreate(['name' => 'view_reports']);
        
        // 2. Create a Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'ronaldjjuuko7@gmail.com'],
            [
                'name' => ' Admin Account',
                'password' => Hash::make('88928892'), 
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]
        );

        // 3. Assign the superadmin role
        $superAdmin->assignRole('admin');
    }
}
