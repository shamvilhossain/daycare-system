<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TherapyServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'SLT Session', 'therapy_type' => 'slt', 'duration_minutes' => 60, 'session_rate' => 1200.00, 'description' => 'Speech and language therapy, 1-on-1.'],
            ['name' => 'ABA Session', 'therapy_type' => 'aba', 'duration_minutes' => 60, 'session_rate' => 1500.00, 'description' => 'Applied Behavior Analysis therapy, 1-on-1.'],
            ['name' => 'OT Session',  'therapy_type' => 'ot',  'duration_minutes' => 60, 'session_rate' => 1200.00, 'description' => 'Occupational therapy, 1-on-1.'],
        ];

        foreach ($services as $service) {
            \App\Models\TherapyService::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}
