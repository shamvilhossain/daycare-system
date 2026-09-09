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
        Schema::table('announcements', function (Blueprint $table) {
            try {
                $table->dropForeign(['staff_id']);
            } catch (\Throwable $e) {
                // Ignore if foreign key did not exist or had different name
            }
        });

        Schema::table('announcements', function (Blueprint $table) {
            try {
                $table->foreign('staff_id')->references('id')->on('users')->cascadeOnDelete();
            } catch (\Throwable $e) {
                // If direct user constraint fails due to data, proceed without blocking
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            try {
                $table->dropForeign(['staff_id']);
                $table->foreign('staff_id')->references('id')->on('staff');
            } catch (\Throwable $e) {
                // Ignore rollback failure
            }
        });
    }
};
