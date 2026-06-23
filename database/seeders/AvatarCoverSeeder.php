<?php

namespace Database\Seeders;

use App\Models\Avatar;
use App\Models\CoverBanner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvatarCoverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $avatar = [
            [
                'image' => 'user/avatar/avatar.png',
            ],
            [
                'image' => 'user/avatar/avatar-1.jpg',
            ],
            [
                'image' => 'user/avatar/avatar-2.jpg',
            ],
            [
                'image' => 'user/avatar/avatar-3.jpg',
            ],
            [
                'image' => 'user/avatar/avatar-4.jpg',
            ],
            [
                'image' => 'user/avatar/avatar-5.jpg',
            ],
            [
                'image' => 'user/avatar/avatar-6.jpg',
            ],
            [
                'image' => 'user/avatar/avatar-7.jpg',
            ],
            [
                'image' => 'user/avatar/avatar-8.jpg',
            ],
            [
                'image' => 'user/avatar/avatar-9.jpg',
            ],
            [
                'image' => 'user/avatar/avatar-10.jpg',
            ],
        ];
        $cover = [
            [
                'image' => 'user/cover_banner/1.jpg',
            ],
            [
                'image' => 'user/cover_banner/2.jpg',
            ],
            [
                'image' => 'user/cover_banner/3.jpg',
            ],
            [
                'image' => 'user/cover_banner/4.jpg',
            ],
            [
                'image' => 'user/cover_banner/5.jpg',
            ],
            [
                'image' => 'user/cover_banner/6.jpg',
            ],
        ];
        Avatar::insert($avatar);
        CoverBanner::insert($cover);
    }
}
