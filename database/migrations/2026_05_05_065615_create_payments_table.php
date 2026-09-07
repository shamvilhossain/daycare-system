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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->bigInteger('parent_id');
            $table->bigInteger('child_id');
            $table->decimal('payable_amount', 10, 2)->nullable();
            $table->decimal('penalty_amount', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2);
            
            // Replaced string with enum. Update the array values to match your business logic.
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'online']);
            
            $table->string('transaction_id', 255)->nullable();
            $table->dateTime('paid_at');
            $table->string('payment_file', 255)->nullable();
            $table->text('remarks')->nullable();
            $table->integer('received_by')->nullable();
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('invoice_id')
                  ->references('id')
                  ->on('invoices')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
