<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            [
                'child_id' => 1,
                'name' => 'Aria Birth Certificate',
                'doc_type' => 'birth_certificate',
                'file_url' => 'documents/aria_birth.pdf',
                'expiry_date' => null,
            ],
            [
                'child_id' => 1,
                'name' => 'Aria Medical Immunization',
                'doc_type' => 'medical_form',
                'file_url' => 'documents/aria_medical.pdf',
                'expiry_date' => '2025-05-10',
            ],
            [
                'child_id' => 2,
                'name' => 'Zayd Birth Certificate',
                'doc_type' => 'birth_certificate',
                'file_url' => 'documents/zayd_birth.pdf',
                'expiry_date' => null,
            ],
            [
                'child_id' => 3,
                'name' => 'Yusuf Custody Info',
                'doc_type' => 'custody_agreement',
                'file_url' => 'documents/yusuf_custody.pdf',
                'expiry_date' => null,
            ],
            [
                'child_id' => 4,
                'name' => 'Sara Allergy Action Plan',
                'doc_type' => 'medical_form',
                'file_url' => 'documents/sara_allergy.pdf',
                'expiry_date' => '2024-12-31',
            ],
        ];

        foreach ($documents as $doc) {
            Document::firstOrCreate(
                [
                    'child_id' => $doc['child_id'],
                    'name'     => $doc['name'],
                ],
                $doc
            );
        }
    }
}
