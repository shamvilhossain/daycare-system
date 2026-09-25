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
        Schema::create('child_therapy_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children');
            $table->foreignId('therapy_package_id')->constrained('therapy_packages');
            $table->decimal('purchase_price', 10, 2); // snapshot — editable at invoice time, per last turn
            $table->date('purchased_at');
            $table->date('expires_at')->nullable();
            $table->enum('status', ['active', 'cancelled'])->default('active'); // exhausted/expired stay computed
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_therapy_packages');
    }
};
