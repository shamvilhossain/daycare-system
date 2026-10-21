<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-time admission fee per child.
 *  - admission_invoice_id: the invoice raised on the child's first enrolment (admission fee + first month)
 *  - admission_fee_waived_at / admission_fee_waiver_reason: fee waived (sibling, staff child, scholarship...)
 * A child is "admitted" (never charged again) when either is set.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->foreignId('admission_invoice_id')->nullable()->after('is_active')
                ->constrained('invoices')->nullOnDelete();
            $table->timestamp('admission_fee_waived_at')->nullable()->after('admission_invoice_id');
            $table->string('admission_fee_waiver_reason')->nullable()->after('admission_fee_waived_at');
        });

        // Children enrolled before the admission fee existed are not charged retroactively
        DB::table('children')
            ->whereIn('id', DB::table('enrollments')->where('status', '!=', 'rejected')->select('child_id'))
            ->update([
                'admission_fee_waived_at'     => now(),
                'admission_fee_waiver_reason' => 'Enrolled before the admission fee was introduced',
            ]);
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admission_invoice_id');
            $table->dropColumn(['admission_fee_waived_at', 'admission_fee_waiver_reason']);
        });
    }
};
