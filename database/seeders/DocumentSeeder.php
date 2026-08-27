<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            [
                'child_id' => 1,
                'name' => 'Aria Birth Certificate',
                'doc_type' => 'birth_certificate',
                'file_url' => 'uploads/documents/aria_birth.pdf',
                'expiry_date' => null,
            ],
            [
                'child_id' => 1,
                'name' => 'Aria Medical Immunization',
                'doc_type' => 'medical_form',
                'file_url' => 'uploads/documents/aria_medical.pdf',
                'expiry_date' => '2025-05-10',
            ],
            [
                'child_id' => 2,
                'name' => 'Zayd Birth Certificate',
                'doc_type' => 'birth_certificate',
                'file_url' => 'uploads/documents/zayd_birth.pdf',
                'expiry_date' => null,
            ],
            [
                'child_id' => 3,
                'name' => 'Yusuf Custody Info',
                'doc_type' => 'custody_agreement',
                'file_url' => 'uploads/documents/yusuf_custody.pdf',
                'expiry_date' => null,
            ],
            [
                'child_id' => 4,
                'name' => 'Sara Allergy Action Plan',
                'doc_type' => 'medical_form',
                'file_url' => 'uploads/documents/sara_allergy.pdf',
                'expiry_date' => '2024-12-31',
            ],
        ];

        foreach ($documents as $doc) {
            DB::table('documents')->insert(array_merge($doc, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
