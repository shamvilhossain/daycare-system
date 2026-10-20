<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Account & data deletion requests (required by Google Play).
     * Submitted from the parent app (logged in) or the public web page.
     */
    public function up(): void
    {
        Schema::create('account_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()
                ->comment('Matched account; null when a web request could not be matched');
            $table->string('email')->comment('Email as entered / of the account at request time');
            $table->string('mobile', 30)->nullable();
            $table->string('requester_name')->nullable();
            $table->text('reason')->nullable();
            $table->enum('source', ['app', 'web'])->default('web');
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_deletion_requests');
    }
};
