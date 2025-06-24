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
        Schema::create('user_topic_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->nullOnDelete();
            $table->foreignId('lesson_id')->constrained()->nullOnDelete();
            $table->foreignId('topic_id')->constrained()->nullOnDelete();
            $table->integer('correct_count');
            $table->integer('total_count');
            $table->decimal('score', 5, 2);
            $table->boolean('passed');
            $table->unsignedInteger('attempt_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_topic_quizzes');
    }
};
