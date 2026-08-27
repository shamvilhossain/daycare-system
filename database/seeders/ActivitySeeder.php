<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            [
                'name' => 'Finger Painting',
                'category' => 'art',
                'description' => 'Creative expression using water-based paint',
                'materials_needed' => 'Paper, non-toxic finger paints, aprons',
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Sing-Along Time',
                'category' => 'music',
                'description' => 'Singing nursery rhymes and clapping games',
                'materials_needed' => 'Song sheets, hand drums',
                'duration_minutes' => 20,
            ],
            [
                'name' => 'Sand Pit Play',
                'category' => 'sensory',
                'description' => 'Sensory exploration with wet and dry sand',
                'materials_needed' => 'Sand toys, buckets, shovels',
                'duration_minutes' => 45,
            ],
            [
                'name' => 'Storytelling Hour',
                'category' => 'reading',
                'description' => 'Reading educational children books aloud',
                'materials_needed' => 'Picture books',
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Building Blocks Challenge',
                'category' => 'motor_skills',
                'description' => 'Building structures to develop fine motor skills',
                'materials_needed' => 'Wooden block sets',
                'duration_minutes' => 40,
            ],
        ];

        foreach ($activities as $act) {
            DB::table('activities')->insert(array_merge($act, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
