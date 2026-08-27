<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $admin = User::firstOrCreate(
            ['email' => 'admin@daycare.com'],
            [
                'password'  => bcrypt('Admin@12345'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        $admin->assignRole('admin');
    }
}
