<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'dashboard_anylisis', 'guard_name' => 'web'],
            ['name' => 'dashboard_todolist', 'guard_name' => 'web'],
            ['name' => 'dashboard_quickAccess', 'guard_name' => 'web'],
            ['name' => 'dashboard_unreadMessages', 'guard_name' => 'web'],
            ['name' => 'setting_events', 'guard_name' => 'web'],
            ['name' => 'setting_admin', 'guard_name' => 'web'],
            ['name' => 'setting_mail', 'guard_name' => 'web'],
            ['name' => 'setting_roleManagement', 'guard_name' => 'web'],
            ['name' => 'user_request', 'guard_name' => 'web'],
            ['name' => 'user_list', 'guard_name' => 'web'],
            ['name' => 'user_create', 'guard_name' => 'web'],
            ['name' => 'user_edit', 'guard_name' => 'web'],
            ['name' => 'user_delete', 'guard_name' => 'web'],
            ['name' => 'user_assignRole', 'guard_name' => 'web'],
            ['name' => 'community_list', 'guard_name' => 'web'],
            ['name' => 'community_create', 'guard_name' => 'web'],
            ['name' => 'community_edit', 'guard_name' => 'web'],
            ['name' => 'community_delete', 'guard_name' => 'web'],
            ['name' => 'team_list', 'guard_name' => 'web'],
            ['name' => 'team_create', 'guard_name' => 'web'],
            ['name' => 'team_edit', 'guard_name' => 'web'],
            ['name' => 'team_delete', 'guard_name' => 'web'],
        ];

        foreach ($permissions as $permission) {
            Permission::insert([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name'],
            ]);
        }
    }
}
