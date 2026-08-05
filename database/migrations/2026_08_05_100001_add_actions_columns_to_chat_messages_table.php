<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreignId('reply_to_id')->nullable()->after('admin_id')
                ->constrained('chat_messages')->nullOnDelete();
            $table->foreignId('forwarded_from_id')->nullable()->after('reply_to_id')
                ->constrained('chat_messages')->nullOnDelete();
            $table->timestamp('pinned_at')->nullable()->after('edited_at');
            $table->foreignId('pinned_by')->nullable()->after('pinned_at')
                ->constrained('admins')->nullOnDelete();
            $table->softDeletes(); // "Delete for me/everyone" support
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reply_to_id');
            $table->dropConstrainedForeignId('forwarded_from_id');
            $table->dropConstrainedForeignId('pinned_by');
            $table->dropColumn(['pinned_at']);
            $table->dropSoftDeletes();
        });
    }
};
