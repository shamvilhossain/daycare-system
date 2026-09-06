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
                'id' => 1,
                'name' => 'Finger Painting',
                'category' => 'art',
                'description' => 'Creative expression using non-toxic water-based paint',
                'materials_needed' => 'Paper, non-toxic finger paints, aprons, water cups',
                'duration_minutes' => 30,
            ],
            [
                'id' => 2,
                'name' => 'Sing-Along Time',
                'category' => 'music',
                'description' => 'Singing nursery rhymes and clapping games to build rhythm and auditory memory',
                'materials_needed' => 'Song sheets, hand drums, tambourines',
                'duration_minutes' => 20,
            ],
            [
                'id' => 3,
                'name' => 'Sand Pit Play',
                'category' => 'sensory',
                'description' => 'Sensory exploration with wet and dry sand, building fine motor skills',
                'materials_needed' => 'Sand toys, buckets, shovels, molds',
                'duration_minutes' => 45,
            ],
            [
                'id' => 4,
                'name' => 'Storytelling Hour',
                'category' => 'reading',
                'description' => 'Reading educational children books aloud and discussing moral themes',
                'materials_needed' => 'Picture books, soft cushions',
                'duration_minutes' => 30,
            ],
            [
                'id' => 5,
                'name' => 'Building Blocks Challenge',
                'category' => 'motor_skills',
                'description' => 'Building towers and bridges to develop hand-eye coordination and spatial reasoning',
                'materials_needed' => 'Wooden block sets, foam blocks',
                'duration_minutes' => 40,
            ],
            [
                'id' => 6,
                'name' => 'Outdoor Obstacle Course',
                'category' => 'outdoor',
                'description' => 'Active playground navigation to improve gross motor skills and teamwork',
                'materials_needed' => 'Cones, soft hurdles, jump ropes',
                'duration_minutes' => 45,
            ],
            [
                'id' => 7,
                'name' => 'Counting & Sorting Colors',
                'category' => 'math',
                'description' => 'Categorizing colorful objects by shape, size, and quantity',
                'materials_needed' => 'Counting bears, color bowls, tweezers',
                'duration_minutes' => 25,
            ],
            [
                'id' => 8,
                'name' => 'Nature & Plant Discovery',
                'category' => 'science',
                'description' => 'Observing leaves, soil, and seeds with magnifying glasses',
                'materials_needed' => 'Magnifying glasses, plant samples, leaf charts',
                'duration_minutes' => 35,
            ],
        ];

        foreach ($activities as $act) {
            DB::table('activities')->updateOrInsert(
                ['id' => $act['id']],
                array_merge($act, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
