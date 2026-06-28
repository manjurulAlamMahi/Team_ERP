<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\Stack;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NewUserSeeder extends Seeder
{
    public function run(): void
    {
        // Remove all non-admin users
        User::where('is_admin', false)->forceDelete();

        // Reset communities and teams
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Team::truncate();
        Community::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Ensure `weekend` column is a string (some DBs may still have old enum type)
        try {
            DB::statement("ALTER TABLE `users` MODIFY `weekend` VARCHAR(20) DEFAULT 'friday'");
        } catch (\Exception $e) {
            // ignore if alter fails (column may not exist or already correct)
        }

        // Ensure stacks used below exist
        $stackMap = [];
        $stacks = [
            'Backend Dev',
            'FrontEnd Dev',
            'Flutter Dev',
            'UI/UX Designer',
        ];

        foreach ($stacks as $s) {
            $stack = Stack::firstOrCreate(['name' => $s], ['status' => 'active']);
            $stackMap[$s] = $stack->id;
        }

        // Top-level users (GM/AGM/OP)
        $topUsers = [
            ['employee_id' => '15019', 'name' => 'Md. Kamruzzaman', 'username' => 'kamruzzaman', 'email' => '15019@example.com', 'role' => 'GM'],
            ['employee_id' => '15053', 'name' => 'Ahsan Habib', 'username' => 'ahsan', 'email' => '15053@example.com', 'role' => 'AGM'],
            ['employee_id' => '15040', 'name' => 'Obaid Ullah', 'username' => 'obaid', 'email' => '15040@example.com', 'role' => 'OP'],
            ['employee_id' => '15208', 'name' => 'Nayeem Alam', 'username' => 'nayeem', 'email' => '15208@example.com', 'role' => 'OP'],
        ];

        foreach ($topUsers as $u) {
            $emp = $u['employee_id'] ?? null;

            // Ensure unique employee id: if collision, generate next EMP-XXXX
            if ($emp && User::withTrashed()->where('employee_id', $emp)->exists()) {
                $lastEmployeeId = User::withTrashed()->orderByDesc('id')->value('employee_id');
                $nextNumber = 1;
                if ($lastEmployeeId && preg_match('/(\d+)$/', $lastEmployeeId, $m)) {
                    $nextNumber = ((int) $m[1]) + 1;
                }
                $emp = 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            $user = User::firstOrCreate(
                ['employee_id' => $emp],
                [
                    'username' => $u['username'],
                    'name' => $u['name'],
                    'email' => $u['email'],
                    'password' => Hash::make('12345678'),
                    'status' => 'active',
                    'employment_status' => 'active',
                ]
            );

            if (!empty($u['role'])) {
                $roleName = $u['role'];
                if ($roleName === 'OP') {
                    $roleName = 'Operation Manager';
                }
                $user->assignRole($roleName);
            }
        }

        // Create a community and the "Night Reaper" team
        $community = Community::firstOrCreate(['name' => 'Night Reaper Community'], ['status' => 'active']);
        $team = Team::firstOrCreate(['name' => 'Night Reaper', 'community_id' => $community->id], ['status' => 'active']);

        // Team members
        $members = [
            ['employee_id' => '15577', 'name' => 'Manjurul Alam Mahi', 'username' => 'mahi', 'email' => 'linktechbd.mahi@gmail.com', 'official_email' => 'manjurul@softvence.com', 'role' => 'Leader', 'stack' => 'Backend Dev', 'weekend' => 'Sunday', 'phone' => '1619833307'],
            ['employee_id' => '15864', 'name' => 'Rashadul Islam', 'username' => 'rashadul', 'email' => 'rashadstack@gmail.com', 'official_email' => 'rashadul@softvence.com', 'role' => 'Co Leader', 'stack' => 'FrontEnd Dev', 'weekend' => 'Friday', 'phone' => '1758214729'],
            ['employee_id' => '16230', 'name' => 'Syed Raihan Ali', 'username' => 'raihan', 'email' => 'raihan.softvence@gmail.com', 'official_email' => 'syed@softvence.com', 'role' => 'Stack Lead', 'stack' => 'Flutter Dev', 'weekend' => 'Thursday', 'phone' => '1841626387'],
            ['employee_id' => '16000', 'name' => 'Md Riad MIah', 'username' => 'riad', 'email' => 'mdriyadpc11@gmail.com', 'official_email' => 'riad.miah@softvence.com', 'role' => 'Stack Lead', 'stack' => 'UI/UX Designer', 'weekend' => 'Sunday', 'phone' => '1590080108'],
            ['employee_id' => '16324', 'name' => 'Md Farhad Mia', 'username' => 'farhad', 'email' => 'farhadmia.cu@gmail.com', 'official_email' => 'farhad.mia@softvence.com', 'role' => 'Stack Lead', 'stack' => 'Backend Dev', 'weekend' => 'Friday', 'phone' => '1772505500'],
            ['employee_id' => '16232', 'name' => 'Mobarok Ali', 'username' => 'mobarok', 'email' => 'immobarokali2001@gmail.com', 'official_email' => 'mobarok@softvence.com', 'role' => 'Member', 'stack' => 'FrontEnd Dev', 'weekend' => 'Sunday', 'phone' => '1612470353'],
            ['employee_id' => '16689', 'name' => 'Md Farhad Reja', 'username' => 'farhadreja', 'email' => 'mdfarhadreja67@gmail.com', 'official_email' => 'farhad@softvence.com', 'role' => 'Member', 'stack' => 'FrontEnd Dev', 'weekend' => 'Sunday', 'phone' => '1404249434'],
            ['employee_id' => '16850', 'name' => 'Md Baki Billah Mahi', 'username' => 'baki', 'email' => 'mahibakibillah@gmail.com', 'official_email' => 'baki@softvence.com', 'role' => 'Member', 'stack' => 'UI/UX Designer', 'weekend' => 'Friday', 'phone' => '1712814628'],
        ];

        foreach ($members as $m) {
            $emp = $m['employee_id'] ?? null;
            if ($emp && User::withTrashed()->where('employee_id', $emp)->exists()) {
                $lastEmployeeId = User::withTrashed()->orderByDesc('id')->value('employee_id');
                $nextNumber = 1;
                if ($lastEmployeeId && preg_match('/(\d+)$/', $lastEmployeeId, $mm)) {
                    $nextNumber = ((int) $mm[1]) + 1;
                }
                $emp = 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            $u = User::firstOrCreate(
                ['employee_id' => $emp],
                [
                    'username' => $m['username'],
                    'name' => $m['name'],
                    'email' => $m['email'],
                    'official_email' => $m['official_email'] ?? null,
                    'phone' => $m['phone'] ?? null,
                    'weekend' => $m['weekend'] ?? null,
                    'team_id' => $team->id,
                    'community_id' => $community->id,
                    'stack_id' => $stackMap[$m['stack']] ?? null,
                    'password' => Hash::make('12345678'),
                    'status' => 'active',
                    'employment_status' => 'active',
                ]
            );

            if (!empty($m['role'])) {
                $roleName = $m['role'];
                if ($roleName === 'OP') {
                    $roleName = 'Operation Manager';
                }
                $u->assignRole($roleName);
            }
        }
    }
}
