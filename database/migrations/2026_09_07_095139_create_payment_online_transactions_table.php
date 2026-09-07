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
        Schema::create('payment_online_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_channel_id');
            
            // Explicitly setting the polymorphic relation to match your exact index name
            $table->string('transactionable_type', 255);
            $table->unsignedBigInteger('transactionable_id');
            $table->index(
                ['transactionable_type', 'transactionable_id'], 
                'transactions_transactionable_type_transactionable_id_index'
            );

            $table->string('transaction_id', 20)->unique('transactions_transaction_id_unique');
            $table->bigInteger('invoice_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('charge_amount', 10, 2)->nullable();
            $table->decimal('platform_fee_amount', 10, 2)->nullable();
            $table->decimal('vat_amount', 10, 2)->nullable();
            
            $table->string('pg_gateway_page_url', 255)->nullable();
            $table->string('pg_session_key', 50)->nullable();
            $table->string('pg_validation_id', 50)->nullable();
            $table->string('pg_bank_transaction_id', 50)->nullable();
            $table->dateTime('pg_transaction_date')->nullable();
            $table->double('pg_amount', 10, 2)->nullable();
            $table->double('pg_store_amount', 10, 2)->nullable();
            $table->string('pg_currency_amount', 11)->nullable();
            $table->string('pg_currency_type', 3)->nullable();
            $table->string('pg_card_no', 30)->nullable();
            $table->string('pg_card_type', 50)->nullable();
            $table->string('pg_card_brand', 30)->nullable();
            $table->string('pg_card_issuer', 30)->nullable();
            $table->string('pg_status', 20)->nullable();
            
            $table->text('initiate_request')->nullable();
            $table->text('initiate_response')->nullable();
            $table->text('success_callback_request')->nullable();
            $table->text('fail_callback_request')->nullable();
            $table->text('cancel_callback_request')->nullable();
            $table->text('ipn_callback_request')->nullable();
            $table->text('remarks')->nullable();
            
            $table->boolean('is_overdue')->default(0);
            $table->tinyInteger('status')->default(0)
                  ->comment('0 = INITIATED, 1 = SUCCESS, 2 = FAILED, 3 = CANCELLED, 4 = CALLBACK RECEIVED, 5 = AMOUNT MISMATCH, 6 = WRONG STATUS');
            $table->timestamps();

            // Foreign Key Constraint matching your custom exact name
            $table->foreign('payment_channel_id', 'transactions_payment_channel_id_foreign')
                  ->references('id')
                  ->on('payment_channels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_online_transactions');
    }
};
