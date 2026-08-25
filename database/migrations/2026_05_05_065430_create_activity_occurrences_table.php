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
        Schema::create('activity_occurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities');
            $table->foreignId('program_id')->constrained('programs');
            $table->foreignId('staff_id')->constrained('staff');

            $table->date('occurrence_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->enum('status', [
                'planned',    // scheduled, hasn't happened yet
                'completed',  // ran fully as planned
                'partial',    // started but cut short
                'cancelled',  // never happened
            ])->default('planned');

            $table->string('materials_used')->nullable();
            // actual materials used — may differ from activities.materials_needed

            $table->text('observations')->nullable();
            // staff notes on how the group activity went overall
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_occurrences');
    }
};
