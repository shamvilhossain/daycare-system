<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityOccurrenceSeeder extends Seeder
{
    public function run(): void
    {
        $occurrences = [
            [
                'activity_id' => 1,
                'program_id' => 1,
                'staff_id' => 1,
                'occurrence_date' => '2023-05-10',
                'start_time' => '10:00:00',
                'end_time' => '10:30:00',
                'status' => 'completed',
                'materials_used' => 'Paper, green and red paints',
                'observations' => 'Kids loved mixing colors',
            ],
            [
                'activity_id' => 2,
                'program_id' => 2,
                'staff_id' => 2,
                'occurrence_date' => '2023-05-10',
                'start_time' => '11:00:00',
                'end_time' => '11:20:00',
                'status' => 'completed',
                'materials_used' => 'Hand drums',
                'observations' => 'Very energetic participation',
            ],
            [
                'activity_id' => 3,
                'program_id' => 3,
                'staff_id' => 3,
                'occurrence_date' => '2023-05-10',
                'start_time' => '15:00:00',
                'end_time' => '15:45:00',
                'status' => 'completed',
                'materials_used' => 'Buckets and shovels',
                'observations' => 'Some sand throwing, redirect needed',
            ],
            [
                'activity_id' => 4,
                'program_id' => 4,
                'staff_id' => 4,
                'occurrence_date' => '2023-05-10',
                'start_time' => '13:00:00',
                'end_time' => '13:30:00',
                'status' => 'partial',
                'materials_used' => 'Adventure book',
                'observations' => 'Shortened due to short attention spans today',
            ],
            [
                'activity_id' => 5,
                'program_id' => 5,
                'staff_id' => 1,
                'occurrence_date' => '2023-05-10',
                'start_time' => '09:00:00',
                'end_time' => '09:40:00',
                'status' => 'completed',
                'materials_used' => 'Wooden blocks',
                'observations' => 'Aria built a high tower successfully',
            ],
        ];

        foreach ($occurrences as $occ) {
            DB::table('activity_occurrences')->insert(array_merge($occ, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
