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
        Schema::table('room_players', function (Blueprint $table) {
            // 先刪除舊的外鍵約束
            $table->dropForeign(['user_id']);
            
            // 重新命名欄位
            $table->renameColumn('user_id', 'member_id');
            
            // 添加新的外鍵約束
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_players', function (Blueprint $table) {
            // 刪除新的外鍵約束
            $table->dropForeign(['member_id']);
            
            // 重新命名欄位回原來的名稱
            $table->renameColumn('member_id', 'user_id');
            
            // 重新添加舊的外鍵約束
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
