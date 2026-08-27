<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            [
                'staff_id' => 5,
                'title' => 'Summer Camp Registration Open',
                'content' => 'Registration for the upcoming Summer Camp is now open for all programs.',
                'audience' => 'parents',
                'published_at' => '2023-05-01 08:00:00',
                'expires_at' => '2023-06-01 18:00:00',
            ],
            [
                'staff_id' => 5,
                'title' => 'Staff Meeting on Friday',
                'content' => 'A mandatory training and review meeting for all staff will be held this Friday at 6 PM.',
                'audience' => 'staff',
                'published_at' => '2023-05-08 09:00:00',
                'expires_at' => '2023-05-12 18:00:00',
            ],
            [
                'staff_id' => 5,
                'title' => 'Daycare Closed for Eid Holiday',
                'content' => 'Please note that the daycare will remain closed during the Eid holidays from June 27th to June 29th.',
                'audience' => 'all',
                'published_at' => '2023-05-15 08:00:00',
                'expires_at' => '2023-06-30 18:00:00',
            ],
            [
                'staff_id' => 5,
                'title' => 'New Healthy Menu Options',
                'content' => 'Starting next week, we are introducing organic fruit snacks to our menu.',
                'audience' => 'parents',
                'published_at' => '2023-05-10 10:00:00',
                'expires_at' => '2023-05-20 18:00:00',
            ],
            [
                'staff_id' => 5,
                'title' => 'Safety Protocol Updates',
                'content' => 'Please review the updated emergency fire drill evacuation map posted in the staff lounge.',
                'audience' => 'staff',
                'published_at' => '2023-05-02 08:00:00',
                'expires_at' => '2023-05-31 18:00:00',
            ],
        ];

        foreach ($announcements as $ann) {
            DB::table('announcements')->insert(array_merge($ann, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
