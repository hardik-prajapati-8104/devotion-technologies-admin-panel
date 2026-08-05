<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->foreignId('last_read_message_id')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['conversation_id', 'admin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_conversation_participants');
    }
};
