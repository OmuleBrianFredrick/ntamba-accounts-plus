<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * companies — one row per workspace (plan: name, currency, time zone,
     * settings). Every M1 table carries a company_id for multi-workspace
     * scoping enforced by a global scope.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('currency', 10)->default('UGX');
            $table->string('timezone', 64)->default('Africa/Kampala');
            $table->string('invoice_prefix', 10)->nullable();
            $table->json('settings')->nullable(); // logo, thresholds, labels, etc.
            $table->boolean('is_training')->default(false); // practice company (setup step 12)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
