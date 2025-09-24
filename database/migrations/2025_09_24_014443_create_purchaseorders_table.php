<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->constrained('permintaan_barangs')->onDelete('cascade');
            $table->foreignId('purchasing_user_id')->constrained('users')->onDelete('cascade');
            $table->string('nomor_po')->unique();
            $table->string('supplier')->nullable();
            $table->decimal('total_harga', 15, 2)->nullable();
            $table->date('tanggal_po');
            $table->enum('status', ['draft', 'sent', 'approved'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->foreign('permintaan_id')->references('id')->on('permintaan_barangs')->onDelete('cascade');
            $table->foreign('purchasing_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
};