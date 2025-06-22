<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transfer_items', function (Blueprint $table) {
            $table->id();
            // References
            $table->foreignId('transfer_id')->constrained('inventory_transfers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // Quantity tracking
            $table->unsignedInteger('requested_quantity');
            $table->unsignedInteger('approved_quantity')->nullable();
            $table->unsignedInteger('shipped_quantity')->nullable();
            $table->unsignedInteger('received_quantity')->nullable();
            
            // Item type and status
            $table->enum('item_type', ['good', 'obsolete']);
            $table->enum('status', [
                'pending',       // Initial state
                'approved',      // Approved by source
                'rejected',      // Rejected by source
                'shipped',       // Shipped from source
                'received',      // Received at destination
                'returned'       // Returned to source
            ])->default('pending');

            // Product details at time of transfer
            $table->string('product_name');    // Store product name at time of transfer
            $table->string('product_code');    // Store product code at time of transfer
            $table->string('unit_name');       // Store unit name at time of transfer
            
            // Notes
            $table->text('rejection_reason')->nullable();
            $table->text('return_reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transfer_items');
    }
};