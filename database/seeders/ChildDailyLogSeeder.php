<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChildDailyLogSeeder extends Seeder
{
    public function run(): void
    {
        $logs = [
            [
                'child_id' => 1,
                'staff_id' => 1,
                'activity_occurrence_id' => 1,
                'log_date' => '2023-05-10',
                'log_type' => 'activity',
                'start_time' => '10:00:00',
                'end_time' => '10:30:00',
                'meal_type' => null,
                'items_served' => null,
                'amount_eaten' => null,
                'quality' => null,
                'is_completed' => true,
                'notes' => 'Participated eagerly in finger painting',
            ],
            [
                'child_id' => 2,
                'staff_id' => 2,
                'activity_occurrence_id' => null,
                'log_date' => '2023-05-10',
                'log_type' => 'nap',
                'start_time' => '12:00:00',
                'end_time' => '13:30:00',
                'meal_type' => null,
                'items_served' => null,
                'amount_eaten' => null,
                'quality' => null,
                'is_completed' => null,
                'notes' => 'Slept soundly',
            ],
            [
                'child_id' => 3,
                'staff_id' => 3,
                'activity_occurrence_id' => null,
                'log_date' => '2023-05-10',
                'log_type' => 'meal',
                'start_time' => '11:30:00',
                'end_time' => '12:00:00',
                'meal_type' => 'lunch',
                'items_served' => 'Macaroni and cheese, apples slices',
                'amount_eaten' => 'All',
                'quality' => 'good',
                'is_completed' => null,
                'notes' => 'Ate very well today',
            ],
            [
                'child_id' => 4,
                'staff_id' => 4,
                'activity_occurrence_id' => null,
                'log_date' => '2023-05-10',
                'log_type' => 'diaper_change',
                'start_time' => '09:30:00',
                'end_time' => null,
                'meal_type' => null,
                'items_served' => null,
                'amount_eaten' => null,
                'quality' => null,
                'is_completed' => null,
                'notes' => 'Wet diaper changed, no rash',
            ],
            [
                'child_id' => 5,
                'staff_id' => 1,
                'activity_occurrence_id' => null,
                'log_date' => '2023-05-10',
                'log_type' => 'incident',
                'start_time' => '14:15:00',
                'end_time' => null,
                'meal_type' => null,
                'items_served' => null,
                'amount_eaten' => null,
                'quality' => null,
                'is_completed' => null,
                'notes' => 'Slipped on playground, minor scratch on left knee, ice applied',
            ],
        ];

        foreach ($logs as $log) {
            DB::table('child_daily_logs')->insert(array_merge($log, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
