<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $enrollments = [
            [
                'child_id' => 1,
                'program_id' => 1,
                'service_type' => 'full_day',
                'status' => 'active',
                'start_date' => '2023-01-01',
                'end_date' => '2024-01-01',
                'notes' => 'First enrollment',
                'created_by' => 1,
                'approved_at' => '2023-01-01 09:00:00',
                'approved_by' => 1,
            ],
            [
                'child_id' => 2,
                'program_id' => 2,
                'service_type' => 'half_day',
                'status' => 'active',
                'start_date' => '2023-02-01',
                'end_date' => null,
                'notes' => null,
                'created_by' => 1,
                'approved_at' => '2023-02-01 10:00:00',
                'approved_by' => 1,
            ],
            [
                'child_id' => 3,
                'program_id' => 5,
                'service_type' => 'full_day',
                'status' => 'pending',
                'start_date' => '2023-09-01',
                'end_date' => null,
                'notes' => 'Awaiting approval',
                'created_by' => 4,
                'approved_at' => null,
                'approved_by' => null,
            ],
            [
                'child_id' => 4,
                'program_id' => 3,
                'service_type' => 'after_school',
                'status' => 'active',
                'start_date' => '2023-03-01',
                'end_date' => '2023-12-31',
                'notes' => null,
                'created_by' => 1,
                'approved_at' => '2023-03-01 08:30:00',
                'approved_by' => 1,
            ],
            [
                'child_id' => 5,
                'program_id' => 4,
                'service_type' => 'drop_in',
                'status' => 'graduated',
                'start_date' => '2022-06-01',
                'end_date' => '2022-12-01',
                'notes' => 'Successfully completed',
                'created_by' => 1,
                'approved_at' => '2022-06-01 09:15:00',
                'approved_by' => 1,
            ],
        ];

        foreach ($enrollments as $enroll) {
            DB::table('enrollments')->insert(array_merge($enroll, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
