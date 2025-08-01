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
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('question_count')->default(10);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('time_limit')->default(60);
            $table->boolean('allow_skip')->default(false);
            $table->boolean('show_explanation')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'question_count',
                'difficulty',
                'time_limit',
                'allow_skip',
                'show_explanation'
            ]);
        });
    }
};
