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
        Schema::create('child_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children');
            $table->foreignId('staff_id')->constrained('staff');
            $table->date('log_date');
            $table->enum('log_type', ['nap','meal','bottle','diaper_change','activity','incident','special_program','medication','other']);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('meal_type', ['breakfast','lunch','snack','bottle'])->nullable();
            $table->string('items_served')->nullable();
            $table->string('amount_eaten')->nullable();
            $table->enum('quality', ['good','fair','poor','refused'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_daily_logs');
    }
};
