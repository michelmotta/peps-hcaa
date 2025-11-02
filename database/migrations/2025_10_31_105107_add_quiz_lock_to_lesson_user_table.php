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
        Schema::table('lesson_user', function (Blueprint $table) {
            $table->foreignId('quiz_locked_topic_id')
                ->nullable()
                ->after('score')
                ->constrained('topics')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_user', function (Blueprint $table) {
            $table->dropForeign(['quiz_locked_topic_id']);
            $table->dropColumn('quiz_locked_topic_id');
        });
    }
};
