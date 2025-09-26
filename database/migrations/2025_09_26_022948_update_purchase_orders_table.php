<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Add new relationship ke permintaan_headers
            $table->foreignId('permintaan_header_id')
                  ->after('id')
                  ->constrained('permintaan_headers')
                  ->onDelete('cascade');
            
            // Add fields untuk multi-item handling
            $table->boolean('include_all_items')->default(true)->after('permintaan_header_id');
            $table->json('selected_item_ids')->nullable()->after('include_all_items');
            
            // Enhanced PO fields
            $table->enum('po_type', ['standard', 'urgent', 'partial'])->default('standard')->after('status');
            $table->text('internal_notes')->nullable()->after('catatan');
            $table->string('reference_number')->nullable()->after('nomor_po');
            
            // Update status enum untuk include new statuses
            $table->enum('status_new', ['draft', 'sent', 'approved', 'rejected', 'cancelled'])->default('draft');
        });
        
        // Copy existing status to new field, then replace
        if (Schema::hasColumn('purchase_orders', 'status')) {
            DB::statement("UPDATE purchase_orders SET status_new = status");
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->renameColumn('status_new', 'status');
            });
        }
        
        echo "✅ purchase_orders table updated\n";
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['permintaan_header_id']);
            $table->dropColumn([
                'permintaan_header_id',
                'include_all_items',
                'selected_item_ids',
                'po_type',
                'internal_notes',
                'reference_number'
            ]);
                // Restore old foreign key (for rollback only)
            $table->foreignId('permintaan_id')->constrained('permintaan_barangs');
        });
    }
};