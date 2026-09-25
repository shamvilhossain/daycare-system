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
        Schema::create('child_therapy_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_therapy_package_id')->constrained('child_therapy_packages');
            $table->foreignId('therapy_service_id')->constrained('therapy_services');
            $table->unsignedInteger('sessions_included'); // snapshot of composition at purchase time
            $table->timestamps();

            $table->unique(['child_therapy_package_id', 'therapy_service_id'], 'child_pkg_items_service_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_therapy_package_items');
    }
};
