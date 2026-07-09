<?php

namespace Database\Seeders;

use App\Models\FiverrProfile;
use Illuminate\Database\Seeder;

class FiverrProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = [
            'devsparrow', 'webflex1', 'Wixbuddy', 'ssexpert_salim', 'Appswizard',
            'Webgenius0', 'swiftech', 'Tech_hype1', 'Devscout', 'Rank_champ',
            'Tech_chips', 'Next_devpro', 'pixelkin', 'webharbor', 'techdot',
            'Wpdesign7', 'Alphasoft', 'TechAce3', 'code_panda', 'byte_benders',
            'wizzardhub', 'Wptech_champ', 'Dev_ninza', 'Wpweb_guru', 'Woow_geeky',
            'Dev_artisan', 'Tech_wix', 'woow_web', 'techpulse', 'pixleehub',
            'ecom_wizards', 'WP_Monkey', 'Web_mastery1', 'growbuddy', 'cmshero',
            'cmswiz', 'Coderton', 'Exa_byte', 'Spark_leap', 'Stream_wave',
            'Pixelwave2', 'brainbox6', 'kraftrix', 'synvex', 'bravizo_',
            'nex_you', 'byteza', 'techluma', 'codvix', 'Pixlora',
            'Cloudfinity', 'devzeno', 'adspike_', 'digital Forge', 'codemint',
            'appvolts', 'wixydev', 'Stack_genius', 'fluttervision', 'appverve',
            'pluvex', 'rank_forge', 'bitlora', 'Brevonex', 'dev_biz1',
            'designstacker', 'tech_cube', 'risbinmridha', 'dev_orio', 'websizzle',
            'adspike', 'devgenie_',
        ];

        foreach ($profiles as $profile) {
            FiverrProfile::firstOrCreate(
                ['name' => $profile],
                ['status' => 'active']
            );
        }
    }
}
