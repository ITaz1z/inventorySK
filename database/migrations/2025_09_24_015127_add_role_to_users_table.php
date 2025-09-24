<?php
// File: database/migrations/xxxx_add_role_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'admin_gudang_umum',
                'admin_gudang_sparepart', 
                'purchasing_1',
                'purchasing_2',
                'general_manager',
                'atasan'
            ])->after('email');
            $table->boolean('is_active')->default(true)->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active']);
        });
    }
};