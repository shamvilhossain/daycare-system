<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['invoice_id' => 1, 'description' => 'Infant Care Tuition - May 2023', 'amount' => 500.00],
            ['invoice_id' => 1, 'description' => 'Meal Plan Fee - May 2023', 'amount' => 50.00],
            ['invoice_id' => 2, 'description' => 'Toddler Playgroup Tuition - May 2023', 'amount' => 350.00],
            ['invoice_id' => 3, 'description' => 'Preschool Program Tuition - May 2023', 'amount' => 450.00],
            ['invoice_id' => 4, 'description' => 'After School Care Tuition - May 2023', 'amount' => 250.00],
        ];

        foreach ($items as $item) {
            DB::table('invoice_items')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
