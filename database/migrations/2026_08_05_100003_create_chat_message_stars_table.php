<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_message_stars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->timestamps();

            // Starring is personal — each admin has their own starred list.
            $table->unique(['message_id', 'admin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_message_stars');
    }
};
