<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parent_child', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')->constrained('parents');
            $table->foreignId('child_id')->constrained('children');

            $table->enum('relationship', [
                'mother', 'father', 'step_parent', 'grandparent', 'legal_guardian', 'other'
            ])->nullable();

            $table->boolean('is_primary')->default(false);

            $table->boolean('can_pickup')->default(false);

            $table->unique(['parent_id', 'child_id']); // prevent duplicate links
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
