<?php

namespace Database\Seeders;

use App\Models\Stack;
use Illuminate\Database\Seeder;

class StackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stacks = [
            'Laravel',
            'MERN',
            'N8N',
            'UI/UX',
            'Flutter',
            'Node',
            'Python',
        ];

        foreach ($stacks as $stack) {
            Stack::firstOrCreate(
                ['name' => $stack],
                ['status' => 'active']
            );
        }
    }
}
