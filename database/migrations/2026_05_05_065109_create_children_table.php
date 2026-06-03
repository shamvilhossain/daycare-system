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
        Schema::create('children', function (Blueprint $table) {
            $table->id();
           
            // ── Child info ──────────────────────────────
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->text('allergies')->nullable();
            $table->text('medical_notes')->nullable();
            $table->string('photo_url')->nullable();
            $table->boolean('is_active')->default(true);

            // ── Primary emergency contact (merged) ──────
            $table->string('ec_name')->nullable()
                ->comment('Emergency contact name');
            $table->string('ec_relationship')->nullable()
                ->comment('e.g. Grandmother, Uncle');
            $table->string('ec_phone')->nullable()
                ->comment('Emergency contact phone');
            $table->boolean('ec_authorized_pickup')->default(false)
                ->comment('Authorized to pick up child');

            // ── Secondary contacts (optional extras) ────
            $table->json('additional_contacts')->nullable()
                ->comment('Array of extra emergency contacts');

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
