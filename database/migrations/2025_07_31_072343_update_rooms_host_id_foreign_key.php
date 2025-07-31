<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // 先刪除舊的外鍵約束
            $table->dropForeign(['host_id']);
            
            // 重新建立外鍵約束，指向 members 表
            $table->foreign('host_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // 刪除新的外鍵約束
            $table->dropForeign(['host_id']);
            
            // 恢復舊的外鍵約束，指向 users 表
            $table->foreign('host_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
