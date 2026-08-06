<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('name');
            $table->text('description')->nullable()->after('avatar');
        });

        Schema::table('chat_conversation_participants', function (Blueprint $table) {
            // Group admins can add/remove members, rename the group, and
            // delete it. The conversation's original creator is always
            // seeded as an admin (see ChatController::store).
            $table->boolean('is_admin')->default(false)->after('admin_id');
        });
    }

    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'description']);
        });

        Schema::table('chat_conversation_participants', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
