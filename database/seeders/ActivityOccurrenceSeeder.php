<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityOccurrenceSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();

        $occurrences = [
            // Historical baseline records (matches ChildDailyLog & Media seeders)
            [
                'id' => 1,
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
                'id' => 2,
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
                'id' => 3,
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
                'id' => 4,
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
                'id' => 5,
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

            // Current Month Operational Calendar records
            // Day 1
            [
                'id' => 6,
                'activity_id' => 1,
                'program_id' => 1,
                'staff_id' => 1,
                'occurrence_date' => $startOfMonth->copy()->toDateString(),
                'start_time' => '09:30:00',
                'end_time' => '10:15:00',
                'status' => 'completed',
                'materials_used' => 'Watercolors, paper sheets, aprons',
                'observations' => 'Great start to the month; sensory painting session went well.',
            ],
            [
                'id' => 7,
                'activity_id' => 7,
                'program_id' => 2,
                'staff_id' => 2,
                'occurrence_date' => $startOfMonth->copy()->toDateString(),
                'start_time' => '11:00:00',
                'end_time' => '11:35:00',
                'status' => 'completed',
                'materials_used' => 'Counting bears, bowls',
                'observations' => 'Children grouped objects by color correctly.',
            ],

            // Day 2
            [
                'id' => 8,
                'activity_id' => 4,
                'program_id' => 1,
                'staff_id' => 1,
                'occurrence_date' => $startOfMonth->copy()->addDays(1)->toDateString(),
                'start_time' => '10:00:00',
                'end_time' => '10:45:00',
                'status' => 'completed',
                'materials_used' => 'Dr. Seuss storybooks',
                'observations' => 'High engagement with rhyming words.',
            ],
            [
                'id' => 9,
                'activity_id' => 6,
                'program_id' => 3,
                'staff_id' => 3,
                'occurrence_date' => $startOfMonth->copy()->addDays(1)->toDateString(),
                'start_time' => '14:30:00',
                'end_time' => '15:15:00',
                'status' => 'completed',
                'materials_used' => 'Cones, soft hurdles',
                'observations' => 'Outdoor obstacle relay race.',
            ],

            // Day 3
            [
                'id' => 10,
                'activity_id' => 2,
                'program_id' => 2,
                'staff_id' => 2,
                'occurrence_date' => $startOfMonth->copy()->addDays(2)->toDateString(),
                'start_time' => '09:45:00',
                'end_time' => '10:15:00',
                'status' => 'completed',
                'materials_used' => 'Hand drums and bells',
                'observations' => 'Rhythm exercises and dance.',
            ],
            [
                'id' => 11,
                'activity_id' => 3,
                'program_id' => 1,
                'staff_id' => 4,
                'occurrence_date' => $startOfMonth->copy()->addDays(2)->toDateString(),
                'start_time' => '15:00:00',
                'end_time' => '15:40:00',
                'status' => 'completed',
                'materials_used' => 'Kinetic sand and animal molds',
                'observations' => 'Calm and focused play.',
            ],

            // Day 4
            [
                'id' => 12,
                'activity_id' => 5,
                'program_id' => 1,
                'staff_id' => 1,
                'occurrence_date' => $startOfMonth->copy()->addDays(3)->toDateString(),
                'start_time' => '10:15:00',
                'end_time' => '11:00:00',
                'status' => 'completed',
                'materials_used' => 'Wooden block sets',
                'observations' => 'Collaborative tower construction.',
            ],
            [
                'id' => 13,
                'activity_id' => 8,
                'program_id' => 2,
                'staff_id' => 2,
                'occurrence_date' => $startOfMonth->copy()->addDays(3)->toDateString(),
                'start_time' => '13:30:00',
                'end_time' => '14:15:00',
                'status' => 'partial',
                'materials_used' => 'Leaves and magnifying glasses',
                'observations' => 'Ended slightly early due to rain outdoor.',
            ],

            // Day 5
            [
                'id' => 14,
                'activity_id' => 1,
                'program_id' => 2,
                'staff_id' => 2,
                'occurrence_date' => $startOfMonth->copy()->addDays(4)->toDateString(),
                'start_time' => '10:00:00',
                'end_time' => '10:45:00',
                'status' => 'completed',
                'materials_used' => 'Sponge painting stamps, butcher paper',
                'observations' => 'Created a group mural.',
            ],

            // Today
            [
                'id' => 15,
                'activity_id' => 4,
                'program_id' => 1,
                'staff_id' => 1,
                'occurrence_date' => $today->toDateString(),
                'start_time' => '09:30:00',
                'end_time' => '10:15:00',
                'status' => 'completed',
                'materials_used' => 'The Very Hungry Caterpillar illustrated book',
                'observations' => 'Interactive session, asked questions about butterflies.',
            ],
            [
                'id' => 16,
                'activity_id' => 2,
                'program_id' => 1,
                'staff_id' => 1,
                'occurrence_date' => $today->toDateString(),
                'start_time' => '11:00:00',
                'end_time' => '11:30:00',
                'status' => 'completed',
                'materials_used' => 'Tambourines, maracas',
                'observations' => 'Energetic sing-along with actions.',
            ],
            [
                'id' => 17,
                'activity_id' => 5,
                'program_id' => 3,
                'staff_id' => 3,
                'occurrence_date' => $today->toDateString(),
                'start_time' => '14:00:00',
                'end_time' => '14:45:00',
                'status' => 'planned',
                'materials_used' => 'Magnetic building tiles',
                'observations' => null,
            ],
            [
                'id' => 18,
                'activity_id' => 6,
                'program_id' => 2,
                'staff_id' => 2,
                'occurrence_date' => $today->toDateString(),
                'start_time' => '15:30:00',
                'end_time' => '16:15:00',
                'status' => 'planned',
                'materials_used' => 'Playground balls and parachute',
                'observations' => null,
            ],

            // Upcoming days in the month
            [
                'id' => 19,
                'activity_id' => 3,
                'program_id' => 1,
                'staff_id' => 1,
                'occurrence_date' => $today->copy()->addDays(2)->toDateString(),
                'start_time' => '10:00:00',
                'end_time' => '10:45:00',
                'status' => 'planned',
                'materials_used' => 'Sand scoops and water wheels',
                'observations' => null,
            ],
            [
                'id' => 20,
                'activity_id' => 7,
                'program_id' => 2,
                'staff_id' => 2,
                'occurrence_date' => $today->copy()->addDays(3)->toDateString(),
                'start_time' => '11:00:00',
                'end_time' => '11:40:00',
                'status' => 'planned',
                'materials_used' => 'Shape sorters and counting beads',
                'observations' => null,
            ],
            [
                'id' => 21,
                'activity_id' => 8,
                'program_id' => 1,
                'staff_id' => 1,
                'occurrence_date' => $today->copy()->addDays(5)->toDateString(),
                'start_time' => '14:00:00',
                'end_time' => '14:45:00',
                'status' => 'planned',
                'materials_used' => 'Seed planting cups and potting soil',
                'observations' => null,
            ],
            [
                'id' => 22,
                'activity_id' => 1,
                'program_id' => 3,
                'staff_id' => 3,
                'occurrence_date' => $today->copy()->addDays(8)->toDateString(),
                'start_time' => '09:30:00',
                'end_time' => '10:15:00',
                'status' => 'planned',
                'materials_used' => 'Finger paints and texture paper',
                'observations' => null,
            ],
            [
                'id' => 23,
                'activity_id' => 4,
                'program_id' => 2,
                'staff_id' => 2,
                'occurrence_date' => $today->copy()->addDays(12)->toDateString(),
                'start_time' => '10:30:00',
                'end_time' => '11:15:00',
                'status' => 'planned',
                'materials_used' => 'Animal kingdom storybook',
                'observations' => null,
            ],
        ];

        foreach ($occurrences as $occ) {
            DB::table('activity_occurrences')->updateOrInsert(
                ['id' => $occ['id']],
                array_merge($occ, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
