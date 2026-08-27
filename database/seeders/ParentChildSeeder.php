<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentChildSeeder extends Seeder
{
    public function run(): void
    {
        $relations = [
            ['parent_id' => 1, 'child_id' => 1, 'relationship' => 'mother', 'is_primary' => true, 'can_pickup' => true],
            ['parent_id' => 2, 'child_id' => 2, 'relationship' => 'father', 'is_primary' => true, 'can_pickup' => true],
            ['parent_id' => 3, 'child_id' => 3, 'relationship' => 'father', 'is_primary' => true, 'can_pickup' => true],
            ['parent_id' => 4, 'child_id' => 4, 'relationship' => 'mother', 'is_primary' => true, 'can_pickup' => true],
            ['parent_id' => 5, 'child_id' => 5, 'relationship' => 'father', 'is_primary' => true, 'can_pickup' => true],
        ];

        foreach ($relations as $rel) {
            DB::table('parent_child')->insert(array_merge($rel, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
