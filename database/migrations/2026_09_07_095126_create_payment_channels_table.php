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
        Schema::create('payment_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('store_id', 30);
            $table->string('store_password', 30);
            $table->string('base_url', 100);
            $table->string('initiate_endpoint', 100);
            $table->string('validate_endpoint', 100);
            $table->string('status_endpoint', 100);
            $table->string('success_redirect_url', 255);
            $table->string('fail_redirect_url', 255);
            $table->string('cancel_redirect_url', 255);
            $table->string('error_redirect_url', 255);
            $table->tinyInteger('status')->default(1)->comment('0 = INACTIVE, 1 = ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_channels');
    }
};
