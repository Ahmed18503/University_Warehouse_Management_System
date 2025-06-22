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
        Schema::create('warehouse_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('warehouse_audits')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // System quantities at the time of audit creation
            $table->integer('system_qty_good')->default(0);
            $table->integer('system_qty_obsolete')->default(0);

            // Physically counted quantities by the auditor
            $table->integer('counted_qty_good')->default(0);
            $table->integer('counted_qty_obsolete')->default(0);

            $table->decimal('unit_cost', 10, 2)->default(0);
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_audit_items');
    }
};
