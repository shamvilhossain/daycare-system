<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityMediaSeeder extends Seeder
{
    public function run(): void
    {
        $media = [
            [
                'activity_occurrence_id' => 1,
                'file_url' => 'uploads/activities/painting1.jpg',
                'media_type' => 'photo',
                'caption' => 'Aria creating her masterpiece',
            ],
            [
                'activity_occurrence_id' => 2,
                'file_url' => 'uploads/activities/singing.mp4',
                'media_type' => 'video',
                'caption' => 'Singing rhymes together',
            ],
            [
                'activity_occurrence_id' => 3,
                'file_url' => 'uploads/activities/sandpit.jpg',
                'media_type' => 'photo',
                'caption' => 'Fun in the sand pit',
            ],
            [
                'activity_occurrence_id' => 5,
                'file_url' => 'uploads/activities/blocks.jpg',
                'media_type' => 'photo',
                'caption' => 'Building the high tower',
            ],
            [
                'activity_occurrence_id' => 1,
                'file_url' => 'uploads/activities/painting2.jpg',
                'media_type' => 'photo',
                'caption' => 'Group painting photo',
            ],
        ];

        foreach ($media as $item) {
            DB::table('activity_media')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
