<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_products', function (Blueprint $table) {
            // Primary key columns for the pivot table
            $table->foreignId('warehouse_id')->constrained('warehouse')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Quantity tracking columns
            $table->unsignedInteger('quantity_good')->default(0);
            $table->unsignedInteger('quantity_obsolete')->default(0);
            $table->unsignedInteger('min_stock_level')->default(0);

            // Timestamps for tracking when quantities change
            $table->timestamps();

            // Make the combination of warehouse_id and product_id the primary key
            $table->primary(['warehouse_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_products');
    }
};
