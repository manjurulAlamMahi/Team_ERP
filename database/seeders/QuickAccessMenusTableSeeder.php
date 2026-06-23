<?php

namespace Database\Seeders;

use App\Models\QuickAccessMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuickAccessMenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            ['user_id' => 1, 'icon' => 'ri-dashboard-2-fill', 'route' => 'dashboard', 'url' => 'http://127.0.0.1:8000/dashboard', 'name' => 'Dashboard'],
            ['user_id' => 1, 'icon' => 'ri-chat-1-fill', 'route' => 'dashboard.inbox', 'url' => 'http://127.0.0.1:8000/dashboard/inbox', 'name' => 'Inbox'],
            ['user_id' => 1, 'icon' => 'ri-user-star-line', 'route' => 'profile.index', 'url' => 'http://127.0.0.1:8000/dashboard/profile', 'name' => 'Profile'],
            ['user_id' => 1, 'icon' => 'ri-calendar-event-line', 'route' => 'events.index', 'url' => 'http://127.0.0.1:8000/dashboard/events', 'name' => 'Events'],
            ['user_id' => 1, 'icon' => 'ri-settings-5-line', 'route' => 'setting.admin', 'url' => 'http://127.0.0.1:8000/dashboard/admin-setting', 'name' => 'Admin'],
            ['user_id' => 1, 'icon' => 'ri-mail-line', 'route' => 'setting.mail', 'url' => 'http://127.0.0.1:8000/dashboard/mail-setting', 'name' => 'Mail'],
            ['user_id' => 1, 'icon' => 'ri-user-settings-line', 'route' => 'role.index', 'url' => 'http://127.0.0.1:8000/dashboard/rolemanagement', 'name' => 'Role Management'],
            ['user_id' => 1, 'icon' => 'ri-group-line', 'route' => 'user.request', 'url' => 'http://127.0.0.1:8000/dashboard/user-request', 'name' => 'User Requests'],
            ['user_id' => 1, 'icon' => 'ri-group-line', 'route' => 'user.list', 'url' => 'http://127.0.0.1:8000/dashboard/user-list', 'name' => 'User List'],
            ['user_id' => 1, 'icon' => 'ri-user-add-line', 'route' => 'user.create', 'url' => 'http://127.0.0.1:8000/dashboard/create-user', 'name' => 'Create User'],
            ['user_id' => 1, 'icon' => 'ri-key-fill', 'route' => 'profile.password', 'url' => 'http://127.0.0.1:8000/dashboard/profile/password', 'name' => 'Change Password'],
        ];

        foreach ($menus as $menu) {
            QuickAccessMenu::insert([
                'user_id' => $menu['user_id'],
                'icon' => $menu['icon'],
                'route' => $menu['route'],
                'url' => $menu['url'],
                'name' => $menu['name']
            ]);
        }
    }
}
