<?php

use App\Models\InvoiceItemPreset;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catalog of reusable invoice line-item names (the "Description" dropdown on
     * admin/invoices/create). New names typed on an invoice are added automatically.
     */
    public function up(): void
    {
        Schema::create('invoice_item_presets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Item name without a trailing "- Month YYYY"');
            $table->decimal('default_amount', 10, 2)->nullable()->comment('Last amount used; pre-fills the amount field');
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('is_active')->default(true)->comment('Hidden from the dropdown when false');
            $table->timestamps();
        });

        // Back-fill from existing manual line items (not therapy sessions / packages).
        $rows = DB::table('invoice_items')
            ->whereNull('therapy_session_id')
            ->whereNull('child_therapy_package_id')
            ->orderBy('id')
            ->get(['description', 'unit_price', 'created_at']);

        $presets = [];
        foreach ($rows as $row) {
            $name = InvoiceItemPreset::normalizeName($row->description);
            if ($name === '') {
                continue;
            }
            $key = mb_strtolower($name);
            $presets[$key] = [
                'name'           => $presets[$key]['name'] ?? $name,
                'default_amount' => $row->unit_price,
                'usage_count'    => ($presets[$key]['usage_count'] ?? 0) + 1,
                'last_used_at'   => $row->created_at,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        if ($presets) {
            DB::table('invoice_item_presets')->insert(array_values($presets));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_item_presets');
    }
};
