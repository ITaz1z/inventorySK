<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permintaan_activities', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('permintaan_header_id')
                  ->constrained('permintaan_headers')
                  ->onDelete('cascade');
            
            $table->foreignId('user_id')->constrained('users');
            
            $table->enum('activity_type', [
                'created',          // Permintaan dibuat
                'submitted',        // Permintaan disubmit
                'item_added',       // Item ditambahkan
                'item_removed',     // Item dihapus  
                'item_approved',    // Item diapprove
                'item_rejected',    // Item ditolak
                'status_changed',   // Status permintaan berubah
                'po_created',       // PO dibuat
                'comment_added',    // Komentar ditambahkan
                'priority_changed'  // Priority diubah
            ]);
            
            $table->string('description');
            $table->json('metadata')->nullable(); // Additional data
            
            $table->timestamps();
            
            $table->index(['permintaan_header_id', 'created_at']);
            $table->index(['user_id', 'activity_type']);
        });
        
        echo "✅ permintaan_activities table created\n";
    }

    public function down()
    {
        Schema::dropIfExists('permintaan_activities');
    }
};

