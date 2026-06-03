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
        Schema::create('enrollments', function (Blueprint $table) {
            
            $table->id();
            $table->foreignId('child_id')->constrained('children');
            $table->foreignId('program_id')->constrained('programs');

            $table->enum('service_type', [
                'full_day',
                'half_day',
                'after_school',
                'drop_in',
            ]);

            $table->enum('status', [
                'pending',    // submitted, awaiting admin approval
                'approved',   // approved, not yet started
                'active',     // currently attending
                'withdrawn',  // left before end date
                'graduated',  // completed the program
                'rejected',   // admin denied the enrollment
            ])->default('pending');

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
