<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('therapy_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();

            $table->foreignId('staff_id')->constrained('staff')->restrictOnDelete();
            // the therapist conducting it — restrict, not cascade, so deleting
            // a staff record can't silently wipe real session history

            $table->foreignId('therapy_service_id')->constrained('therapy_services')->restrictOnDelete();

            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            // stored explicitly rather than derived from therapy_services.duration_minutes —
            // a session running long/short shouldn't require touching the catalog

            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'no_show'])
                ->default('scheduled');

            $table->text('notes')->nullable();
            // therapist's session notes — progress, observations, etc.

            $table->foreignId('booked_by')->nullable()->constrained('users')->nullOnDelete();
            // who created the booking — admin or the therapist themself; same
            // audit-trail idea as enrollments.enrolled_by from earlier

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapy_sessions');
    }
};
