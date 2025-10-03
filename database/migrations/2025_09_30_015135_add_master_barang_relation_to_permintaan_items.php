<?php
// File: database/migrations/2025_09_29_100001_add_master_barang_relation_to_permintaan_items.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('permintaan_items', function (Blueprint $table) {
            // Tambah relasi ke master_barangs (nullable untuk backward compatibility)
            $table->foreignId('master_barang_id')
                  ->nullable()
                  ->after('permintaan_header_id')
                  ->constrained('master_barangs')
                  ->onDelete('cascade');
            
            // Tambah field untuk tracking stok saat permintaan dibuat
            $table->integer('stok_tersedia_saat_request')->nullable()->after('jumlah_disetujui');
            $table->boolean('ada_stok_mencukupi')->default(true)->after('stok_tersedia_saat_request');
            
            // Tambah field untuk menandai apakah item ini menggunakan stok atau request baru
            $table->enum('tipe_permintaan', ['dari_stok', 'request_baru'])->default('request_baru')->after('ada_stok_mencukupi');
            
            // Index untuk performance
            $table->index(['master_barang_id', 'tipe_permintaan']);
            $table->index(['ada_stok_mencukupi', 'status']);
        });
        
        echo "✅ master_barang relation added to permintaan_items\n";
    }

    public function down()
    {
        Schema::table('permintaan_items', function (Blueprint $table) {
            $table->dropForeign(['master_barang_id']);
            $table->dropColumn([
                'master_barang_id',
                'stok_tersedia_saat_request',
                'ada_stok_mencukupi',
                'tipe_permintaan'
            ]);
        });
    }
};