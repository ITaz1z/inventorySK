<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permintaan_items', function (Blueprint $table) {
            $table->id();
            
            // Relationship
            $table->foreignId('permintaan_header_id')
                  ->constrained('permintaan_headers')
                  ->onDelete('cascade');
            
            // Item Details
            $table->string('nama_barang');
            $table->enum('kategori', ['umum', 'sparepart']);
            $table->integer('jumlah');
            $table->string('satuan', 50);
            $table->text('keterangan')->nullable();
            
            // Image Storage
            $table->string('gambar_path')->nullable();
            $table->json('gambar_metadata')->nullable(); // size, type, original_name
            
            // Item Status & Review
            $table->enum('status', ['pending', 'approved', 'rejected', 'partial'])->default('pending');
            $table->text('catatan_review')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            
            // Approved quantity (untuk partial approval)
            $table->integer('jumlah_disetujui')->nullable();
            
            // Ordering & Priority
            $table->integer('urutan')->default(1);
            $table->boolean('is_urgent')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['permintaan_header_id', 'status']);
            $table->index(['kategori', 'status']);
            $table->index('reviewed_at');
            $table->index('urutan');
        });
        
        echo "✅ permintaan_items table created\n";
    }

    public function down()
    {
        Schema::dropIfExists('permintaan_items');
    }
};
