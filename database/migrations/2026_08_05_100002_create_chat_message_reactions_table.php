<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->string('emoji', 8);
            $table->timestamps();

            // One reaction per admin per message (picking a new emoji
            // replaces the old one — matches WhatsApp/Slack behaviour).
            $table->unique(['message_id', 'admin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_message_reactions');
    }
};
