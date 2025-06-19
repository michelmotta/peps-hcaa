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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Original file name
            $table->string('path');  // File path in storage
            $table->string('mime_type');  // MIME type of the file
            $table->bigInteger('size');  // Size of the file in bytes
            $table->string('extension', 10)->nullable();  // File extension (e.g., .jpg, .pdf)
            $table->text('description')->nullable();  // Optional description for the file
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
