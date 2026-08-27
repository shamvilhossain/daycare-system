<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $parents = [
            ['email' => 'fatima.rahman@example.com',   'first_name' => 'Fatima', 'last_name' => 'Rahman',    'mobile' => '01711-000001', 'nid' => '1234567890', 'occupation' => 'Teacher', 'city' => 'Dhaka', 'address' => 'Mirpur, Dhaka'],
            ['email' => 'karim.chowdhury@example.com',  'first_name' => 'Karim',  'last_name' => 'Chowdhury', 'mobile' => '01711-000002', 'nid' => '1234567891', 'occupation' => 'Engineer', 'city' => 'Dhaka', 'address' => 'Uttara, Dhaka'],
            ['email' => 'rahim.ahmed@example.com',      'first_name' => 'Rahim',  'last_name' => 'Ahmed',     'mobile' => '01711-000003', 'nid' => '1234567892', 'occupation' => 'Doctor', 'city' => 'Chittagong', 'address' => 'Halishahar, Chittagong'],
            ['email' => 'sabina.yasmin@example.com',    'first_name' => 'Sabina', 'last_name' => 'Yasmin',    'mobile' => '01711-000004', 'nid' => '1234567893', 'occupation' => 'Banker', 'city' => 'Sylhet', 'address' => 'Zindabazar, Sylhet'],
            ['email' => 'arif.hasan@example.com',      'first_name' => 'Arif',   'last_name' => 'Hasan',     'mobile' => '01711-000005', 'nid' => '1234567894', 'occupation' => 'Businessperson', 'city' => 'Rajshahi', 'address' => 'Motihar, Rajshahi'],
        ];

        foreach ($parents as $data) {
            $user = User::create([
                'email'     => $data['email'],
                'password'  => bcrypt('password'),
                'role'      => 'parent',
                'is_active' => true,
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('parent');
            }

            DB::table('parents')->insert([
                'user_id'    => $user->id,
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'mobile'     => $data['mobile'],
                'nid'        => $data['nid'],
                'occupation' => $data['occupation'],
                'city'       => $data['city'],
                'address'    => $data['address'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
