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
                'amount_paid' => 500.00,
                'payment_method' => 'credit_card',
                'transaction_id' => 'TXN_CREDIT_99881',
                'payment_date' => '2023-05-10 14:00:00',
            ],
            [
                'invoice_id' => 1,
                'amount_paid' => 50.00,
                'payment_method' => 'cash',
                'transaction_id' => 'TXN_CASH_001',
                'payment_date' => '2023-05-11 09:30:00',
            ],
            [
                'invoice_id' => 2,
                'amount_paid' => 350.00,
                'payment_method' => 'bank_transfer',
                'transaction_id' => 'TXN_BANK_88221',
                'payment_date' => '2023-05-09 11:15:00',
            ],
            [
                'invoice_id' => 3,
                'amount_paid' => 100.00,
                'payment_method' => 'credit_card',
                'transaction_id' => 'TXN_CREDIT_99882',
                'payment_date' => '2023-05-14 16:45:00',
            ],
            [
                'invoice_id' => 5,
                'amount_paid' => 300.00,
                'payment_method' => 'bank_transfer',
                'transaction_id' => 'TXN_BANK_88222',
                'payment_date' => '2023-05-08 10:00:00',
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
