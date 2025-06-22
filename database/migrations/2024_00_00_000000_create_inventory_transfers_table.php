<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {       
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();
            // Warehouse references
            $table->foreignId('source_warehouse_id')->constrained('warehouse')->onDelete('cascade');
            $table->foreignId('destination_warehouse_id')->constrained('warehouse')->onDelete('cascade');
            
            // User tracking for each step
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->foreignId('source_approved_by_user_id')->nullable()->constrained('users');
            $table->foreignId('destination_approved_by_user_id')->nullable()->constrained('users');
            $table->foreignId('shipped_by_user_id')->nullable()->constrained('users');
            $table->foreignId('received_by_user_id')->nullable()->constrained('users');
            
            // Status management
            $table->enum('status', [
                'draft',                    // Initial state when transfer is being created
                'pending_source_approval',   // Waiting for source warehouse approval
                'source_rejected',          // Rejected by source warehouse
                'source_approved',          // Approved by source warehouse
                'shipped',                  // Items shipped from source
                'received',                 // Items received at destination
                'completed',                // Transfer completed successfully
                'cancelled'                 // Transfer cancelled
            ])->default('draft');

            // Additional info
            $table->text('notes')->nullable();          // General notes about the transfer
            $table->text('rejection_reason')->nullable(); // Reason if transfer is rejected
            $table->text('return_notes')->nullable();    // Notes about any returns
            
            // Timestamps for each stage
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('source_approved_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transfers');
    }
};