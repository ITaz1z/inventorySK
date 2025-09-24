<?php
// File: database/migrations/xxxx_create_permintaan_barangs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permintaan_barangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nama_barang');
            $table->enum('kategori', ['umum', 'sparepart']);
            $table->integer('jumlah');
            $table->string('satuan', 20);
             $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'review', 'approved', 'rejected'])->default('pending');
            $table->timestamp('tanggal_butuh')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permintaan_barangs');
    }
};