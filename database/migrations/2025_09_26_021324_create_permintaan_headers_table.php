<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permintaan_headers', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nomor_permintaan')->unique();
            $table->string('judul_permintaan');
            
            // Dates & Priority
            $table->date('tanggal_permintaan');
            $table->date('tanggal_dibutuhkan');
            $table->enum('tingkat_prioritas', ['urgent', 'penting', 'routine', 'non_routine']);
            
            // Status Tracking
            $table->enum('status', [
                'draft',        // Belum submit
                'pending',      // Menunggu review
                'review',       // Sedang direview
                'partial',      // Sebagian approved
                'approved',     // Semua approved
                'rejected',     // Ditolak
                'po_created'    // Sudah ada PO
            ])->default('draft');
            
            // Additional Info
            $table->text('catatan_permintaan')->nullable();
            
            // Counters (akan diupdate otomatis)
            $table->integer('total_items')->default(0);
            $table->integer('approved_items')->default(0);
            $table->integer('rejected_items')->default(0);
            
            // Workflow timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes untuk performance
            $table->index(['user_id', 'status']);
            $table->index(['tingkat_prioritas', 'status']);
            $table->index('tanggal_dibutuhkan');
            $table->index('submitted_at');
        });
        
        echo "✅ permintaan_headers table created\n";
    }

    public function down()
    {
        Schema::dropIfExists('permintaan_headers');
    }
};