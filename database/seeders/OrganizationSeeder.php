<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\Team;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $communityTeams = [
            'Syndicate' => 'Alpha Team',
            'Oral' => 'Beta Team',
            'Phantom' => 'Gamma Team',
        ];

        foreach ($communityTeams as $communityName => $teamName) {
            $community = Community::firstOrCreate(
                ['name' => $communityName],
                ['status' => 'active']
            );

            Team::firstOrCreate(
                ['name' => $teamName, 'community_id' => $community->id],
                ['status' => 'active']
            );
        }
    }
}
