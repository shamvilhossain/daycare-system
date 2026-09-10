<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staffMembers = [
            [
                'email'          => 'tania.staff@example.com',
                'first_name'     => 'Tania',
                'last_name'      => 'Sultana',
                'role'           => 'teacher',
                'department'     => 'daycare',
                'specialization' => null,
                'nid'            => '9876543210',
                'dob'            => '1990-05-15',
                'hire'           => '2023-01-10',
                'note'           => 'Senior lead teacher with 5+ years early childhood care experience.',
            ],
            [
                'email'          => 'mizan.staff@example.com',
                'first_name'     => 'Mizan',
                'last_name'      => 'Rahman',
                'role'           => 'teacher',
                'department'     => 'daycare',
                'specialization' => null,
                'nid'            => '9876543211',
                'dob'            => '1992-08-20',
                'hire'           => '2023-02-15',
                'note'           => 'Junior teacher focusing on creative arts and story time.',
            ],
            [
                'email'          => 'farhana.staff@example.com',
                'first_name'     => 'Farhana',
                'last_name'      => 'Akter',
                'role'           => 'assistant',
                'department'     => 'daycare',
                'specialization' => null,
                'nid'            => '9876543212',
                'dob'            => '1995-12-01',
                'hire'           => '2023-05-01',
                'note'           => 'Assistant teacher supporting toddler room activities.',
            ],
            [
                'email'          => 'kamrul.staff@example.com',
                'first_name'     => 'Kamrul',
                'last_name'      => 'Islam',
                'role'           => 'assistant',
                'department'     => 'daycare',
                'specialization' => null,
                'nid'            => '9876543213',
                'dob'            => '1994-03-10',
                'hire'           => '2023-06-20',
                'note'           => 'Assistant teacher supporting outdoor play and meal supervision.',
            ],
            [
                'email'          => 'nasrin.staff@example.com',
                'first_name'     => 'Nasrin',
                'last_name'      => 'Jahan',
                'role'           => 'admin',
                'department'     => 'daycare',
                'specialization' => null,
                'nid'            => '9876543214',
                'dob'            => '1988-11-25',
                'hire'           => '2022-09-01',
                'note'           => 'Office administrator coordinating admissions and staffing.',
            ],
            [
                'email'          => 'samira.staff@example.com',
                'first_name'     => 'Samira',
                'last_name'      => 'Khan',
                'role'           => 'therapist',
                'department'     => 'therapy',
                'specialization' => 'slt',
                'nid'            => '9876543215',
                'dob'            => '1991-04-12',
                'hire'           => '2023-03-01',
                'note'           => 'Licensed Speech & Language Pathologist (SLT) specializing in child communication development.',
            ],
            [
                'email'          => 'arif.staff@example.com',
                'first_name'     => 'Arif',
                'last_name'      => 'Hasan',
                'role'           => 'therapist',
                'department'     => 'therapy',
                'specialization' => 'aba',
                'nid'            => '9876543216',
                'dob'            => '1993-07-18',
                'hire'           => '2023-04-15',
                'note'           => 'Certified Applied Behavior Analysis (ABA) specialist working with neurodiverse children.',
            ],
            [
                'email'          => 'nusrat.staff@example.com',
                'first_name'     => 'Nusrat',
                'last_name'      => 'Parveen',
                'role'           => 'therapist',
                'department'     => 'therapy',
                'specialization' => 'ot',
                'nid'            => '9876543217',
                'dob'            => '1989-10-30',
                'hire'           => '2023-01-20',
                'note'           => 'Occupational Therapist (OT) focusing on fine motor skills and sensory integration.',
            ],
        ];

        foreach ($staffMembers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'password'  => bcrypt('password'),
                    'role'      => 'staff',
                    'is_active' => true,
                ]
            );

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('staff');
            }

            DB::table('staff')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'first_name'     => $data['first_name'],
                    'last_name'      => $data['last_name'],
                    'role'           => $data['role'],
                    'department'     => $data['department'],
                    'specialization' => $data['specialization'],
                    'nid'            => $data['nid'],
                    'date_of_birth'  => $data['dob'],
                    'hire_date'      => $data['hire'],
                    'note'           => $data['note'],
                    'is_active'      => true,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]
            );
        }
    }
}
