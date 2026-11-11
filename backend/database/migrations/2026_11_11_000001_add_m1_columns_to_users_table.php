<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * M1 Foundation: extend the users table with the fields from the plan's
     * "Data tables" section — role, agent code, phone/PIN sign-in, company
     * scoping and active flag. Email becomes nullable (agents sign in with
     * phone + PIN and may not have an email).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id');
            $table->string('role')->default('agent')->after('name'); // agent | accountant | production_manager | admin
            $table->string('phone', 20)->nullable()->unique()->after('email');
            $table->string('pin_hash')->nullable()->after('password');
            $table->string('agent_code', 10)->nullable()->after('phone'); // GLO, WIN, FAR
            $table->boolean('is_active')->default(true)->after('agent_code');
            $table->unsignedTinyInteger('pin_failed_attempts')->default(0)->after('is_active');
            $table->timestamp('pin_locked_at')->nullable()->after('pin_failed_attempts');

            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_id', 'role', 'phone', 'pin_hash',
                'agent_code', 'is_active', 'pin_failed_attempts', 'pin_locked_at',
            ]);
            $table->string('email')->nullable(false)->change();
        });
    }
};
