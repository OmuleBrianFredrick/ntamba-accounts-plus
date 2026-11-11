<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * products — the price list (code, name, size, category, tracks expiry,
     * unit price, unit cost, active, external id). Unit cost is hidden from
     * agents; prices are whole UGX shillings.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('size')->nullable();   // e.g. 500g
            $table->string('category')->nullable(); // coffee, dried fruit, honey
            $table->boolean('tracks_expiry')->default(false);
            $table->unsignedBigInteger('unit_price'); // whole UGX
            $table->unsignedBigInteger('unit_cost')->nullable(); // optional in Phase 1
            $table->boolean('is_active')->default(true);
            $table->string('external_id')->nullable()->index(); // Phase 3 imports
            $table->timestamps();

            $table->unique(['company_id', 'name', 'size'], 'products_name_size_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
