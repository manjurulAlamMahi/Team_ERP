<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin',
            'Leader',
            'Co Leader',
            'Stack Lead',
            'Member',
            'Probation',
            // Future roles: DB support only, no business logic yet
            'GM',
            'AGM',
            'Operation Manager',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $admin = User::firstOrCreate(
            [
                'email' => 'admin@example.com',
            ],
            [
                'employee_id' => 'EMP-0001',
                'username' => 'admin',
                'name' => 'Admin',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'employment_status' => 'active',
                'is_admin' => true,
            ]
        );

        $admin->assignRole('Admin');
    }
}
