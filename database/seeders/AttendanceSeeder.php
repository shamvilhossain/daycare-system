<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $attendance = [
            [
                'child_id' => 1,
                'program_id' => 1,
                'attendance_date' => '2023-05-10',
                'check_in_time' => '08:05:00',
                'check_out_time' => '16:55:00',
                'status' => 'present',
                'notes' => 'On time',
            ],
            [
                'child_id' => 2,
                'program_id' => 2,
                'attendance_date' => '2023-05-10',
                'check_in_time' => '08:45:00',
                'check_out_time' => '12:30:00',
                'status' => 'late',
                'notes' => 'Stuck in traffic',
            ],
            [
                'child_id' => 3,
                'program_id' => 5,
                'attendance_date' => '2023-05-10',
                'check_in_time' => null,
                'check_out_time' => null,
                'status' => 'absent',
                'notes' => 'Fever',
            ],
            [
                'child_id' => 4,
                'program_id' => 3,
                'attendance_date' => '2023-05-10',
                'check_in_time' => '14:00:00',
                'check_out_time' => '18:00:00',
                'status' => 'present',
                'notes' => null,
            ],
            [
                'child_id' => 5,
                'program_id' => 4,
                'attendance_date' => '2023-05-10',
                'check_in_time' => '09:15:00',
                'check_out_time' => '15:45:00',
                'status' => 'present',
                'notes' => 'Drop in session',
            ],
        ];

        foreach ($attendance as $record) {
            DB::table('attendance')->insert(array_merge($record, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
