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
        Schema::table('order_products', function (Blueprint $table) {
            $table->string('product_name')->after('product_id');
            $table->text('product_description')->nullable()->after('product_name');
            $table->decimal('variant_weight', 8, 2)->after('total_weight');
            $table->enum('variant_unit', ['kg', 'gm', 'pc'])->after('variant_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
        });
    }
};