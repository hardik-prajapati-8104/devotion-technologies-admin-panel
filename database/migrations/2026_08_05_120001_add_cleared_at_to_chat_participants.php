<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_conversation_participants', function (Blueprint $table) {
            // "Clear Chat" no longer deletes messages — it just hides
            // everything before this timestamp for THIS participant only.
            // Other participants still see full history.
            $table->timestamp('cleared_at')->nullable()->after('last_read_message_id');
        });
    }

    public function down(): void
    {
        Schema::table('chat_conversation_participants', function (Blueprint $table) {
            $table->dropColumn('cleared_at');
        });
    }
};
