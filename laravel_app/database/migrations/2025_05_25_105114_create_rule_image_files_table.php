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
        Schema::create('rule_image_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_book_id')->constrained('rule_books')->onDelete('cascade');
            $table->string('rule_image_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_image_files');
    }
};
