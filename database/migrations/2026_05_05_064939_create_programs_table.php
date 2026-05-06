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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('service_type', ['full_day','half_day','after_school','drop_in']);
            $table->enum('billing_model', ['monthly','daily','hourly']);
            $table->unsignedSmallInteger('min_age_months');
            $table->unsignedSmallInteger('max_age_months');
            $table->unsignedSmallInteger('capacity');
            $table->decimal('monthly_fee', 8, 2)->nullable();
            $table->decimal('daily_rate', 8, 2)->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->time('day_start_time')->nullable();
            $table->time('day_end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
