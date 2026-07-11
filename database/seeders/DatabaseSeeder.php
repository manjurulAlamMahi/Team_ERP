<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(StackSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RoleAndUserSeeder::class);
        $this->call(NewUserSeeder::class);
        $this->call(OrganizationSeeder::class);
        $this->call(OperationManagerSeeder::class);
        $this->call(AvatarCoverSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(EventsTableSeeder::class);
        $this->call(QuickAccessMenusTableSeeder::class);
        $this->call(FiverrProfileSeeder::class);
        $this->call(ClientMessageTypeSeeder::class);

    }
}
