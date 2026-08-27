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
            ['email' => 'tania.staff@example.com', 'first_name' => 'Tania', 'last_name' => 'Sultana', 'role' => 'teacher', 'nid' => '9876543210', 'dob' => '1990-05-15', 'hire' => '2023-01-10', 'note' => 'Senior teacher'],
            ['email' => 'mizan.staff@example.com', 'first_name' => 'Mizan', 'last_name' => 'Rahman', 'role' => 'teacher', 'nid' => '9876543211', 'dob' => '1992-08-20', 'hire' => '2023-02-15', 'note' => 'Junior teacher'],
            ['email' => 'farhana.staff@example.com', 'first_name' => 'Farhana', 'last_name' => 'Akter', 'role' => 'assistant', 'nid' => '9876543212', 'dob' => '1995-12-01', 'hire' => '2023-05-01', 'note' => 'Assistant teacher'],
            ['email' => 'kamrul.staff@example.com', 'first_name' => 'Kamrul', 'last_name' => 'Islam', 'role' => 'assistant', 'nid' => '9876543213', 'dob' => '1994-03-10', 'hire' => '2023-06-20', 'note' => 'Assistant teacher'],
            ['email' => 'nasrin.staff@example.com', 'first_name' => 'Nasrin', 'last_name' => 'Jahan', 'role' => 'admin', 'nid' => '9876543214', 'dob' => '1988-11-25', 'hire' => '2022-09-01', 'note' => 'Office administrator'],
        ];

        foreach ($staffMembers as $data) {
            $user = User::create([
                'email'     => $data['email'],
                'password'  => bcrypt('password'),
                'role'      => 'staff',
                'is_active' => true,
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('staff');
            }

            DB::table('staff')->insert([
                'user_id'       => $user->id,
                'first_name'    => $data['first_name'],
                'last_name'     => $data['last_name'],
                'role'          => $data['role'],
                'nid'           => $data['nid'],
                'date_of_birth' => $data['dob'],
                'hire_date'     => $data['hire'],
                'note'          => $data['note'],
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
