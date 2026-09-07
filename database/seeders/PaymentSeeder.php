<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $payments = [
            [
                'invoice_id' => 1,
                'payable_amount' => 500.00,
                'paid_amount' => 500.00,
                'payment_method' => 'cash',
                'transaction_id' => 'TXN_CREDIT_99881',
                'paid_at' => '2023-05-10 14:00:00',
            ],
            [
                'invoice_id' => 2,
                'payable_amount' => 350.00,
                'paid_amount' => 350.00,
                'payment_method' => 'card',
                'transaction_id' => 'TXN_BANK_88221',
                'paid_at' => '2023-05-09 11:15:00',
            ],
            [
                'invoice_id' => 3,
                'payable_amount' => 100.00,
                'paid_amount' => 100.00,
                'payment_method' => 'online',
                'transaction_id' => 'Bkash_123456789',
                'paid_at' => '2023-05-14 16:45:00',
            ],
            [
                'invoice_id' => 5,
                'payable_amount' => 300.00,
                'paid_amount' => 300.00,
                'payment_method' => 'bank_transfer',
                'transaction_id' => 'TXN_BANK_88222',
                'paid_at' => '2023-05-08 10:00:00',
            ],
        ];

        foreach ($payments as $pay) {
            DB::table('payments')->insert(array_merge($pay, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
