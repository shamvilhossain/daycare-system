<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        // Use user ID 1 (Admin) or existing ID 1
        $staffId = 1;

        $announcements = [
            [
                'staff_id'     => $staffId,
                'title'        => 'Summer Camp 2026 Registration Now Open!',
                'content'      => "Exciting news for families! Registration for our 2026 Summer Adventure Camp is officially open. We offer hands-on science, outdoor sports, creative arts, and splash play. Secure your child's spot early as seats are limited across all age groups.",
                'audience'     => 'parents',
                'published_at' => now()->subDays(2),
                'expires_at'   => now()->addDays(45),
            ],
            [
                'staff_id'     => $staffId,
                'title'        => 'Daycare Special Holiday Schedule & Center Closing Notice',
                'content'      => 'Please note that our daycare center will be closed for the upcoming public holiday. Regular daycare operations and scheduled activities will resume promptly on the following business day. For any urgent emergencies, please contact our support desk.',
                'audience'     => 'all',
                'published_at' => now()->subDay(),
                'expires_at'   => now()->addDays(20),
            ],
            [
                'staff_id'     => $staffId,
                'title'        => 'Fresh & Organic Nutrition Menu Launching Next Week',
                'content'      => "Starting next Monday, we are upgrading our daily meal plan with freshly sourced organic seasonal fruits, farm-fresh dairy snacks, and nut-free balanced warm meals certified by pediatric nutritionists. Check with staff for dietary preferences.",
                'audience'     => 'parents',
                'published_at' => now()->subHours(8),
                'expires_at'   => now()->addDays(30),
            ],
            [
                'staff_id'     => $staffId,
                'title'        => 'Mandatory Monthly Staff Training & Safety Drill Review',
                'content'      => 'A mandatory training and review meeting for all teachers and care assistants will be held this Friday at 5:30 PM. We will cover updated CPR guidelines and playground emergency protocols.',
                'audience'     => 'staff',
                'published_at' => now()->subDays(3),
                'expires_at'   => now()->addDays(7),
            ],
            [
                'staff_id'     => $staffId,
                'title'        => 'Annual Pediatric Health & Dental Checkup Camp',
                'content'      => 'Our annual complimentary pediatric dental and vision screening camp for all enrolled children will take place next month. Detailed consent forms have been sent to registered parents.',
                'audience'     => 'all',
                'published_at' => now()->subHours(12),
                'expires_at'   => now()->addDays(60),
            ],
        ];

        foreach ($announcements as $ann) {
            DB::table('announcements')->updateOrInsert(
                ['title' => $ann['title']],
                array_merge($ann, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
