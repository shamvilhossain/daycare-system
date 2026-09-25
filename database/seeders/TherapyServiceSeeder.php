<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TherapyServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'OT Session',  'therapy_type' => 'ot',  'duration_minutes' => 60, 'session_rate' => 800.00,  'description' => 'Occupational therapy, 1-on-1.'],
            ['name' => 'SLT Session', 'therapy_type' => 'slt', 'duration_minutes' => 60, 'session_rate' => 900.00,  'description' => 'Speech and language therapy, 1-on-1.'],
            ['name' => 'ABA Session', 'therapy_type' => 'aba', 'duration_minutes' => 60, 'session_rate' => 1000.00, 'description' => 'Applied Behavior Analysis therapy, 1-on-1.'],
        ];

        foreach ($services as $service) {
            // updateOrCreate — not firstOrCreate — so this actually corrects
            // the placeholder prices already sitting in your database.
            \App\Models\TherapyService::updateOrCreate(['name' => $service['name']], $service);
        }
    }
}
