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
        Schema::create('child_found_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_safety_tag_id')->constrained('child_safety_tags')->cascadeOnDelete();
            $table->string('reporter_name')->nullable();
            $table->string('reporter_phone')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'notified', 'resolved'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_found_reports');
    }
};
