<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::insert([
            [
                'name'       => 'New Year',
                'start_date' => '2025-01-01',
                'end_date'   => '2025-01-01',
                'message'    => 'Happy New Year! 🎉',
            ],
            [
                'name'       => 'International Mother Language Day',
                'start_date' => '2025-02-21',
                'end_date'   => '2025-02-21',
                'message'    => 'Remembering our language martyrs. 🖤',
            ],
            [
                'name'       => 'Independence Day',
                'start_date' => '2025-03-26',
                'end_date'   => '2025-03-26',
                'message'    => 'Happy Independence Day! 🙌',
            ],
            [
                'name'       => 'Bengali New Year',
                'start_date' => '2025-04-14',
                'end_date'   => '2025-04-14',
                'message'    => 'Shubho Noboborsho! 🎊',
            ],
            [
                'name'       => 'May Day',
                'start_date' => '2025-05-01',
                'end_date'   => '2025-05-01',
                'message'    => 'Happy International Workers\' Day! 💪',
            ],
            [
                'name'       => 'Buddha Purnima',
                'start_date' => '2025-05-11',
                'end_date'   => '2025-05-11',
                'message'    => 'Happy Buddha Purnima! ☸️',
            ],
            [
                'name'       => 'Eid-ul-Fitr',
                'start_date' => '2025-03-30',
                'end_date'   => '2025-04-01',
                'message'    => 'Eid Mubarak! 🌙✨',
            ],
            [
                'name'       => 'Eid-ul-Adha',
                'start_date' => '2025-06-06',
                'end_date'   => '2025-06-08',
                'message'    => 'Eid Mubarak! 🐑',
            ],
            [
                'name'       => 'Ashura',
                'start_date' => '2025-06-23',
                'end_date'   => '2025-06-23',
                'message'    => 'Observing Ashura. ☪️',
            ],
            [
                'name'       => 'Janmashtami',
                'start_date' => '2025-08-16',
                'end_date'   => '2025-08-16',
                'message'    => 'Happy Janmashtami! 🦚',
            ],
            [
                'name'       => 'Eid-e-Milad-un-Nabi',
                'start_date' => '2025-09-05',
                'end_date'   => '2025-09-05',
                'message'    => 'Remembering the Prophet Muhammad (PBUH). 🌙',
            ],
            [
                'name'       => 'Durga Puja - Maha Navami',
                'start_date' => '2025-10-01',
                'end_date'   => '2025-10-01',
                'message'    => 'Happy Maha Navami! 🏵️',
            ],
            [
                'name'       => 'Durga Puja - Vijaya Dashami',
                'start_date' => '2025-10-02',
                'end_date'   => '2025-10-02',
                'message'    => 'Shubho Bijoya! 🌺',
            ],
            [
                'name'       => 'Victory Day',
                'start_date' => '2025-12-16',
                'end_date'   => '2025-12-16',
                'message'    => 'Victory Day! ✌️',
            ],
            [
                'name'       => 'Christmas',
                'start_date' => '2025-12-25',
                'end_date'   => '2025-12-25',
                'message'    => 'Merry Christmas! 🎄',
            ],
        ]);

    }
}
