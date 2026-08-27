<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,   // roles/permissions first 
            AdminSeeder::class,          

            ParentSeeder::class, // creates User + parents row together
            StaffSeeder::class,  // creates User + staff row together
            ProgramSeeder::class,
            ChildSeeder::class,
            ParentChildSeeder::class,

            EnrollmentSeeder::class,
            AttendanceSeeder::class,
            DocumentSeeder::class,

            ActivitySeeder::class,
            ActivityOccurrenceSeeder::class,
            ChildDailyLogSeeder::class,
            ActivityMediaSeeder::class,

            InvoiceSeeder::class,
            InvoiceItemSeeder::class,
            PaymentSeeder::class,

            AnnouncementSeeder::class,
        ]);
    }
}
