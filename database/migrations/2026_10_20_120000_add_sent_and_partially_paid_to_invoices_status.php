<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds invoice statuses:
     *  - sent:           a payment link was sent to the parent
     *  - partially_paid: some, but not all, of the invoice has been paid
     * Also back-fills partially_paid for existing open invoices that already have payments.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['draft', 'sent', 'partially_paid', 'paid', 'overdue', 'cancelled'])
                ->default('draft')
                ->change();
        });

        DB::table('invoices')
            ->where('status', 'draft')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('payments')
                    ->whereColumn('payments.invoice_id', 'invoices.id');
            })
            ->update(['status' => 'partially_paid']);
    }

    public function down(): void
    {
        DB::table('invoices')->whereIn('status', ['sent', 'partially_paid'])->update(['status' => 'draft']);

        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['draft', 'paid', 'overdue', 'cancelled'])
                ->default('draft')
                ->change();
        });
    }
};
