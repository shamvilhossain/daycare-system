<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Child;
use App\Models\Staff;
use App\Models\TherapyService;
use App\Models\TherapySession;
use App\Models\User;

class TherapySessionSeeder extends Seeder
{
    public function run(): void
    {
        $children = Child::where('is_active', true)->get();
        $admin = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first()
            ?? User::where('role', 'admin')->first();

        $sltTherapist = Staff::where('specialization', 'slt')->first();
        $abaTherapist = Staff::where('specialization', 'aba')->first();
        $otTherapist  = Staff::where('specialization', 'ot')->first();

        $sltService = TherapyService::where('therapy_type', 'slt')->first();
        $abaService = TherapyService::where('therapy_type', 'aba')->first();
        $otService  = TherapyService::where('therapy_type', 'ot')->first();

        if (!$sltTherapist || !$abaTherapist || !$otTherapist || !$sltService || !$abaService || !$otService || $children->isEmpty()) {
            return;
        }

        $sessions = [
            // SLT sessions — Samira Khan
            [
                'child_id'           => $children[0]->id ?? 1,
                'staff_id'           => $sltTherapist->id,
                'therapy_service_id' => $sltService->id,
                'session_date'       => now()->subDays(5)->format('Y-m-d'),
                'start_time'         => '09:00',
                'end_time'           => '10:00',
                'status'             => 'completed',
                'notes'              => 'Good progress on articulation exercises. Child is improving consonant blends.',
                'booked_by'          => $admin?->id,
            ],
            [
                'child_id'           => $children[1]->id ?? 2,
                'staff_id'           => $sltTherapist->id,
                'therapy_service_id' => $sltService->id,
                'session_date'       => now()->subDays(3)->format('Y-m-d'),
                'start_time'         => '10:30',
                'end_time'           => '11:30',
                'status'             => 'completed',
                'notes'              => 'Focused on vocabulary building through picture cards. Great engagement.',
                'booked_by'          => $admin?->id,
            ],
            [
                'child_id'           => $children[0]->id ?? 1,
                'staff_id'           => $sltTherapist->id,
                'therapy_service_id' => $sltService->id,
                'session_date'       => now()->addDays(2)->format('Y-m-d'),
                'start_time'         => '09:00',
                'end_time'           => '10:00',
                'status'             => 'scheduled',
                'notes'              => 'Follow-up on articulation. Plan to introduce new exercises.',
                'booked_by'          => $admin?->id,
            ],

            // ABA sessions — Arif Hasan
            [
                'child_id'           => $children[2]->id ?? 3,
                'staff_id'           => $abaTherapist->id,
                'therapy_service_id' => $abaService->id,
                'session_date'       => now()->subDays(4)->format('Y-m-d'),
                'start_time'         => '11:00',
                'end_time'           => '12:00',
                'status'             => 'completed',
                'notes'              => 'DTT session focusing on social interactions. Positive reinforcement working well.',
                'booked_by'          => $admin?->id,
            ],
            [
                'child_id'           => $children[0]->id ?? 1,
                'staff_id'           => $abaTherapist->id,
                'therapy_service_id' => $abaService->id,
                'session_date'       => now()->subDays(1)->format('Y-m-d'),
                'start_time'         => '14:00',
                'end_time'           => '15:00',
                'status'             => 'no_show',
                'notes'              => 'Child was absent. Parent notified for rescheduling.',
                'booked_by'          => $admin?->id,
            ],
            [
                'child_id'           => $children[1]->id ?? 2,
                'staff_id'           => $abaTherapist->id,
                'therapy_service_id' => $abaService->id,
                'session_date'       => now()->addDays(1)->format('Y-m-d'),
                'start_time'         => '11:00',
                'end_time'           => '12:00',
                'status'             => 'scheduled',
                'notes'              => 'Initial ABA assessment session.',
                'booked_by'          => $admin?->id,
            ],

            // OT sessions — Nusrat Parveen
            [
                'child_id'           => $children[1]->id ?? 2,
                'staff_id'           => $otTherapist->id,
                'therapy_service_id' => $otService->id,
                'session_date'       => now()->subDays(2)->format('Y-m-d'),
                'start_time'         => '13:00',
                'end_time'           => '14:00',
                'status'             => 'completed',
                'notes'              => 'Fine motor skill exercises — cutting, tracing. Child showed improved grip.',
                'booked_by'          => $admin?->id,
            ],
            [
                'child_id'           => $children[2]->id ?? 3,
                'staff_id'           => $otTherapist->id,
                'therapy_service_id' => $otService->id,
                'session_date'       => now()->subDays(6)->format('Y-m-d'),
                'start_time'         => '15:00',
                'end_time'           => '16:00',
                'status'             => 'cancelled',
                'notes'              => 'Cancelled due to therapist leave.',
                'booked_by'          => $admin?->id,
            ],
            [
                'child_id'           => $children[2]->id ?? 3,
                'staff_id'           => $otTherapist->id,
                'therapy_service_id' => $otService->id,
                'session_date'       => now()->addDays(3)->format('Y-m-d'),
                'start_time'         => '13:00',
                'end_time'           => '14:00',
                'status'             => 'scheduled',
                'notes'              => 'Sensory integration assessment scheduled.',
                'booked_by'          => $admin?->id,
            ],
            [
                'child_id'           => $children[0]->id ?? 1,
                'staff_id'           => $otTherapist->id,
                'therapy_service_id' => $otService->id,
                'session_date'       => now()->addDays(5)->format('Y-m-d'),
                'start_time'         => '10:00',
                'end_time'           => '11:00',
                'status'             => 'scheduled',
                'notes'              => 'Follow-up OT session for sensory processing.',
                'booked_by'          => $admin?->id,
            ],
        ];

        foreach ($sessions as $sessionData) {
            TherapySession::firstOrCreate(
                [
                    'child_id'    => $sessionData['child_id'],
                    'staff_id'    => $sessionData['staff_id'],
                    'session_date' => $sessionData['session_date'],
                    'start_time'  => $sessionData['start_time'],
                ],
                $sessionData
            );
        }
    }
}
