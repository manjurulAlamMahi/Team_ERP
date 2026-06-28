<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OperationManagerSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'name' => 'Nayeem Alam',
                'community' => 'Phantom',
                'email' => 'nayeem.alam@phantom.com',
                'phone_code' => '+880',
                'phone' => '1712345678',
                'designation' => 'Operation Manager',
                'joining_date' => '2024-03-05',
            ],
            [
                'name' => 'Sabbir Hossain',
                'community' => 'Phantom',
                'email' => 'sabbir.hossain@phantom.com',
                'phone_code' => '+880',
                'phone' => '1712345679',
                'designation' => 'Operation Manager',
                'joining_date' => '2024-04-10',
            ],
            [
                'name' => 'Rakib Hasan',
                'community' => 'Oral',
                'email' => 'rakib.hasan@oral.com',
                'phone_code' => '+880',
                'phone' => '1723456780',
                'designation' => 'Operation Manager',
                'joining_date' => '2024-05-15',
            ],
            [
                'name' => 'Md. Saidur Rahman',
                'community' => 'Oral',
                'email' => 'saidur.rahman@oral.com',
                'phone_code' => '+880',
                'phone' => '1723456781',
                'designation' => 'Operation Manager',
                'joining_date' => '2024-06-01',
            ],
            [
                'name' => 'Jobaed Hossain',
                'community' => 'Syndicate',
                'email' => 'jobaed.hossain@syndicate.com',
                'phone_code' => '+880',
                'phone' => '1734567890',
                'designation' => 'Operation Manager',
                'joining_date' => '2024-06-20',
            ],
        ];

        foreach ($entries as $entry) {
            $community = Community::firstOrCreate(
                ['name' => $entry['community']],
                ['status' => 'active']
            );

            $user = User::firstOrNew([
                'email' => $entry['email'],
            ]);

            if (!$user->exists || !$user->employee_id) {
                $user->employee_id = $this->generateEmployeeId();
            }

            $user->username = $user->username ?: strtolower(preg_replace('/[^a-z0-9\.]/', '', str_replace(' ', '.', $entry['name'])));
            $user->name = $entry['name'];
            $user->official_email = $entry['email'];
            $user->phone_code = $entry['phone_code'];
            $user->phone = $entry['phone'];
            $user->designation = $entry['designation'];
            $user->joining_date = Carbon::parse($entry['joining_date']);
            $user->employment_status = 'active';
            $user->status = 'active';
            $user->community_id = $community->id;
            $user->is_admin = false;
            $user->is_request = false;
            $user->password = $user->exists ? $user->password : Hash::make('12345678');
            $user->email_verified_at = $user->email_verified_at ?: Carbon::now();
            $user->save();

            $user->assignRole('Operation Manager');
        }
    }

    protected function generateEmployeeId(): string
    {
        $lastEmployeeId = User::withTrashed()
            ->whereNotNull('employee_id')
            ->orderByDesc('id')
            ->value('employee_id');

        $nextNumber = 1;

        if ($lastEmployeeId && preg_match('/(\d+)$/', $lastEmployeeId, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
