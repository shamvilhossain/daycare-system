<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('therapy_session_id')->nullable()->constrained('therapy_sessions')->nullOnDelete();
            // nullable — only set for therapy line items; null for daycare/manual items
            // nullOnDelete — deleting a session shouldn't wipe invoice history
            $table->foreignId('child_therapy_package_id')
                    ->nullable()
                    ->constrained('child_therapy_packages')
                    ->nullOnDelete();

            $table->string('description')->comment('e.g., tuition, Late Pickup Fee, SLT Session.');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            // price snapshot — copied from therapy_services.session_rate at billing time;
            // past invoices reflect what was actually charged, not current rates

            $table->decimal('amount', 10, 2);
            // amount = quantity × unit_price (pre-computed for convenience)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
