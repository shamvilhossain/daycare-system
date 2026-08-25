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
        Schema::create('activity_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_occurrence_id')->constrained('activity_occurrences');
            $table->string('file_url');
            $table->enum('media_type', ['photo', 'video']);
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_media');
    }
};
