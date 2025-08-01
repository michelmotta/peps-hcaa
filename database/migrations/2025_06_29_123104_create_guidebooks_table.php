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
        Schema::create('guidebooks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->longText('description');
            $table->foreignId('guidebook_category_id')->constrained('guidebook_categories')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guidebooks');
    }
};
