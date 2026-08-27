<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Infant Care',
                'service_type' => 'full_day',
                'billing_model' => 'monthly',
                'min_age_months' => 3,
                'max_age_months' => 12,
                'capacity' => 10,
                'monthly_fee' => 500.00,
                'daily_rate' => 25.00,
                'hourly_rate' => 5.00,
                'day_start_time' => '08:00:00',
                'day_end_time' => '17:00:00',
            ],
            [
                'name' => 'Toddler Playgroup',
                'service_type' => 'half_day',
                'billing_model' => 'monthly',
                'min_age_months' => 13,
                'max_age_months' => 36,
                'capacity' => 15,
                'monthly_fee' => 350.00,
                'daily_rate' => 18.00,
                'hourly_rate' => 4.00,
                'day_start_time' => '08:30:00',
                'day_end_time' => '12:30:00',
            ],
            [
                'name' => 'After School Care',
                'service_type' => 'after_school',
                'billing_model' => 'monthly',
                'min_age_months' => 72,
                'max_age_months' => 144,
                'capacity' => 20,
                'monthly_fee' => 250.00,
                'daily_rate' => 15.00,
                'hourly_rate' => 3.50,
                'day_start_time' => '14:00:00',
                'day_end_time' => '18:00:00',
            ],
            [
                'name' => 'Drop-in Daycare',
                'service_type' => 'drop_in',
                'billing_model' => 'hourly',
                'min_age_months' => 12,
                'max_age_months' => 120,
                'capacity' => 8,
                'monthly_fee' => null,
                'daily_rate' => 30.00,
                'hourly_rate' => 6.00,
                'day_start_time' => '09:00:00',
                'day_end_time' => '16:00:00',
            ],
            [
                'name' => 'Preschool Program',
                'service_type' => 'full_day',
                'billing_model' => 'monthly',
                'min_age_months' => 37,
                'max_age_months' => 71,
                'capacity' => 18,
                'monthly_fee' => 450.00,
                'daily_rate' => 22.00,
                'hourly_rate' => 4.50,
                'day_start_time' => '08:00:00',
                'day_end_time' => '16:30:00',
            ],
        ];

        foreach ($programs as $prog) {
            DB::table('programs')->insert(array_merge($prog, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
