<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * audit_log — every change (user, action, table, record, before, after,
     * reason, device, time). Append-only: records are voided, never deleted.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');               // created | updated | voided | signed_in | ...
            $table->string('table_name');
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->text('reason')->nullable();     // required for edits/voids by admin/accountant
            $table->string('device', 120)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['table_name', 'record_id']);
            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
