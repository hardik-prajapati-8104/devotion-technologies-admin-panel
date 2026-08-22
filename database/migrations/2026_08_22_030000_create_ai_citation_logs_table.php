<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Every row is one detected AI crawler visit or AI-referred human visit,
 * written by App\Http\Middleware\LogAiVisibility. Powers the "who's
 * actually looking at / citing this site" stats on the AI Visibility
 * dashboard.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_citation_logs')) {
            Schema::create('ai_citation_logs', function (Blueprint $table) {
                $table->id();

                // Name of the bot (e.g. "GPTBot") or the referring AI
                // product's domain (e.g. "chat.openai.com").
                $table->string('source', 100)->index();

                // 'crawler'  = a known AI bot fetched this page directly
                // 'referral' = a human arrived via a link from an AI product
                $table->enum('type', ['crawler', 'referral'])->index();

                $table->string('url_path', 500);
                $table->text('user_agent')->nullable();
                $table->string('referrer', 500)->nullable();
                $table->string('ip', 45)->nullable();

                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_citation_logs');
    }
};
