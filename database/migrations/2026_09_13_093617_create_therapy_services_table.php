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
     
        Schema::create('therapy_services', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            // e.g. "SLT Session", "ABA 1-Hour Session", "OT Assessment"

            $table->enum('therapy_type', ['slt', 'aba', 'ot']);
            // Matches staff.specialization exactly — used in Step 3 to only
            // let a therapist be booked for services matching their specialty.

            $table->text('description')->nullable();

            $table->unsignedSmallInteger('duration_minutes')->default(60);
            // Session length — feeds directly into overlap-detection logic
            // in TherapySessionService (Step 3).

            $table->decimal('session_rate', 8, 2);
            // Per-session billing rate — flows into invoice_items.subtotal
            // when a completed session gets invoiced (Step 4).

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapy_services');
    }
};
