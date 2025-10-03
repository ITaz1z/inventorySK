<?php
// File: database/migrations/2025_09_29_100000_create_master_barangs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('master_barangs', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang');
            $table->enum('kategori', ['umum', 'sparepart']);
            $table->string('satuan', 50);
            $table->text('deskripsi')->nullable();
            
            // Stock Management
            $table->integer('stok_minimum')->default(0);
            $table->integer('stok_maksimum')->nullable();
            $table->integer('stok_tersedia')->default(0);
            $table->integer('stok_reserved')->default(0);
            
            // Additional Info
            $table->string('lokasi_gudang', 100)->nullable();
            $table->decimal('harga_rata_rata', 15, 2)->nullable();
            $table->string('supplier_utama', 200)->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->enum('status_stok', ['normal', 'minimum', 'habis', 'over_stock'])->default('normal');
            
            // Audit Fields
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['kategori', 'is_active']);
            $table->index(['stok_tersedia', 'stok_minimum']);
            $table->index('nama_barang');
            $table->index('kode_barang');
        });
        
        echo "✅ master_barangs table created\n";
    }

    public function down()
    {
        Schema::dropIfExists('master_barangs');
    }
};