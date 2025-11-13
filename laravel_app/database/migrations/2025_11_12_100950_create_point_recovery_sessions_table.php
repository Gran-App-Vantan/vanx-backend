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
        Schema::create('point_recovery_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->integer('amount');
            $table->string('service_name');
            $table->text('description');
            $table->enum('type', ['get','use','import','export','expire'])->default('get');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_recovery_sessions');
    }
};
