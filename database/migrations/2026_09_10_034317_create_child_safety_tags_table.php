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
        Schema::create('child_safety_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->string('token', 40)->unique();   // random, unguessable — used in the QR URL
            $table->string('label')->nullable();      // "Backpack", "School shirt"
            $table->boolean('is_active')->default(true); // revoke a lost/compromised tag without touching the child
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_safety_tags');
    }
};
