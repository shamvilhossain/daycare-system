<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    // How they work together:

    // Generate the Bill: You create a record in invoices for $550.

    // Itemize it: You create two records in invoice_items connected to that invoice (Tuition: $500, Meals: $50).

    // Get Paid: When the parent pays, you create a record in payments and update the status column on the invoices table to paid.

    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents');
            $table->foreignId('child_id')->constrained('children');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['draft','paid','overdue','cancelled'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
