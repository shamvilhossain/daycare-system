<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TherapyService;
use App\Models\TherapyPackage;
use App\Models\TherapyPackageItem;


class TherapyPackageSeeder extends Seeder
{
    public function run(): void
    {
        $ot  = TherapyService::where('therapy_type', 'ot')->first();
        $slt = TherapyService::where('therapy_type', 'slt')->first();
        $aba = TherapyService::where('therapy_type', 'aba')->first();

        if (!$ot || !$slt || !$aba) {
            $this->command->warn('Run TherapyServiceSeeder first.');
            return;
        }

        // Only seeding the packages you've given real prices for so far.
        // SLT/ABA single-tier packages, OT 12/16/20, and the 4/4/4 and 8/8/8
        // combos don't have confirmed prices yet — add those through the
        // admin UI once you've settled on them, rather than me guessing.
        $packages = [
            ['name' => 'OT — 4 Session Package', 'price' => 3000.00,
             'items' => [['service' => $ot, 'count' => 4]]],

            ['name' => 'OT — 8 Session Package', 'price' => 5800.00,
             'items' => [['service' => $ot, 'count' => 8]]],

            ['name' => 'Custom Combo — 4 OT + 6 ABA + 8 SLT', 'price' => 15000.00,
             'items' => [
                 ['service' => $ot, 'count' => 4],
                 ['service' => $aba, 'count' => 6],
                 ['service' => $slt, 'count' => 8],
             ]],
        ];

        foreach ($packages as $data) {
            $package = TherapyPackage::firstOrCreate(
                ['name' => $data['name']],
                ['price' => $data['price'], 'is_active' => true]
            );

            foreach ($data['items'] as $item) {
                TherapyPackageItem::firstOrCreate(
                    ['therapy_package_id' => $package->id, 'therapy_service_id' => $item['service']->id],
                    ['session_count' => $item['count']]
                );
            }
        }
    }
}
