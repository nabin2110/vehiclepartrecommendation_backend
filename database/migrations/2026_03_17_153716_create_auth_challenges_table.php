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
        Schema::create('auth_challenges', function (Blueprint $table) {
            $table->id();
            $table->uuid('challenge_id')->unique();
            $table->string('email', 255)->index();
            $table->string('device_id', 255);
            $table->string('nonce', 64);
            $table->string('ip_address', 45);
            $table->string('user_agent', 512)->nullable();
            $table->boolean('used')->default(false)->index();
            $table->unsignedTinyInteger('attempt_count')->default(0);
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_challenges');
    }
};
