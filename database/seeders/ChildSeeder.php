<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChildSeeder extends Seeder
{
    public function run(): void
    {
        $children = [
            [
                'first_name' => 'Aria',
                'last_name' => 'Rahman',
                'date_of_birth' => '2022-04-12',
                'allergies' => 'Peanuts',
                'medical_notes' => 'Asthma',
                'ec_name' => 'Zakir Rahman',
                'ec_relationship' => 'Uncle',
                'ec_phone' => '01711-999001',
                'ec_authorized_pickup' => true,
            ],
            [
                'first_name' => 'Zayd',
                'last_name' => 'Chowdhury',
                'date_of_birth' => '2021-08-20',
                'allergies' => null,
                'medical_notes' => 'None',
                'ec_name' => 'Laila Chowdhury',
                'ec_relationship' => 'Grandmother',
                'ec_phone' => '01711-999002',
                'ec_authorized_pickup' => true,
            ],
            [
                'first_name' => 'Yusuf',
                'last_name' => 'Ahmed',
                'date_of_birth' => '2023-01-15',
                'allergies' => 'Milk',
                'medical_notes' => 'Eczema',
                'ec_name' => 'Rafiq Ahmed',
                'ec_relationship' => 'Grandfather',
                'ec_phone' => '01711-999003',
                'ec_authorized_pickup' => false,
            ],
            [
                'first_name' => 'Sara',
                'last_name' => 'Yasmin',
                'date_of_birth' => '2019-10-05',
                'allergies' => null,
                'medical_notes' => null,
                'ec_name' => 'Nabila Yasmin',
                'ec_relationship' => 'Aunt',
                'ec_phone' => '01711-999004',
                'ec_authorized_pickup' => true,
            ],
            [
                'first_name' => 'Ryan',
                'last_name' => 'Hasan',
                'date_of_birth' => '2020-03-30',
                'allergies' => 'Eggs',
                'medical_notes' => null,
                'ec_name' => 'Imran Hasan',
                'ec_relationship' => 'Uncle',
                'ec_phone' => '01711-999005',
                'ec_authorized_pickup' => true,
            ],
        ];

        foreach ($children as $child) {
            DB::table('children')->insert(array_merge($child, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
