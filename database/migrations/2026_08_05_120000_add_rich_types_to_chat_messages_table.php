<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds first-class support for rich message types on top of the
     * existing plain-text / single-attachment schema.
     *
     * `type` drives how the bubble renders: text, document, image, video,
     * audio, contact, poll, event.
     *
     * `meta` carries type-specific structured data that doesn't belong in
     * dedicated columns (poll question/options/votes, contact card fields,
     * event details, voice-note duration). Kept as JSON rather than new
     * tables per type to avoid a proliferation of near-empty tables for
     * what is, for every type except polls, a handful of fields that are
     * never queried relationally — only ever read back onto one bubble.
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('type', 20)->default('text')->after('attachment');
            $table->string('attachment_name')->nullable()->after('type');
            $table->string('attachment_mime')->nullable()->after('attachment_name');
            $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_mime');
            $table->json('meta')->nullable()->after('attachment_size');

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'attachment_name', 'attachment_mime', 'attachment_size', 'meta']);
        });
    }
};
