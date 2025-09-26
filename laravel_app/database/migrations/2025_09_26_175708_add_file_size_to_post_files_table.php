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
        Schema::table('post_files', function (Blueprint $table) {
            $table->bigInteger('file_size')->nullable()->after('post_file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_files', function (Blueprint $table) {
            $table->dropColumn('file_size');
        });
    }
};
