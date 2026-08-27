<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = [
            [
                'parent_id' => 1,
                'child_id' => 1,
                'invoice_number' => 'INV-2023-001',
                'invoice_date' => '2023-05-01',
                'due_date' => '2023-05-15',
                'total_amount' => 550.00,
                'status' => 'paid',
            ],
            [
                'parent_id' => 2,
                'child_id' => 2,
                'invoice_number' => 'INV-2023-002',
                'invoice_date' => '2023-05-01',
                'due_date' => '2023-05-15',
                'total_amount' => 350.00,
                'status' => 'paid',
            ],
            [
                'parent_id' => 3,
                'child_id' => 3,
                'invoice_number' => 'INV-2023-003',
                'invoice_date' => '2023-05-01',
                'due_date' => '2023-05-15',
                'total_amount' => 450.00,
                'status' => 'overdue',
            ],
            [
                'parent_id' => 4,
                'child_id' => 4,
                'invoice_number' => 'INV-2023-004',
                'invoice_date' => '2023-05-01',
                'due_date' => '2023-05-15',
                'total_amount' => 250.00,
                'status' => 'draft',
            ],
            [
                'parent_id' => 5,
                'child_id' => 5,
                'invoice_number' => 'INV-2023-005',
                'invoice_date' => '2023-05-01',
                'due_date' => '2023-05-15',
                'total_amount' => 300.00,
                'status' => 'cancelled',
            ],
        ];

        foreach ($invoices as $inv) {
            DB::table('invoices')->insert(array_merge($inv, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
