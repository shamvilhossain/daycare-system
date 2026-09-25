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
        Schema::create('therapy_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapy_package_id')->constrained('therapy_packages');
            $table->foreignId('therapy_service_id')->constrained('therapy_services');
            $table->unsignedInteger('session_count');
            $table->timestamps();

            $table->unique(['therapy_package_id', 'therapy_service_id'], 'pkg_items_package_service_unique'); // one row per type per package
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapy_package_items');
    }
};
