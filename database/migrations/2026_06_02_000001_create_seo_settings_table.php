<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();

            // Unique indexed column
            $table->string('page_key', 191)->unique();

            $table->string('page_label', 191);

            $table->string('seo_title', 191)->nullable();

            $table->string('meta_description', 500)->nullable();

            $table->string('focus_keyword', 191)->nullable();

            $table->string('canonical_url', 500)->nullable();

            $table->string('robots_meta', 191)->default('index, follow');

            $table->string('og_title', 191)->nullable();

            $table->string('og_description', 500)->nullable();

            $table->string('og_image', 191)->nullable();

            $table->string('twitter_title', 191)->nullable();

            $table->string('twitter_description', 500)->nullable();

            $table->string('twitter_image', 191)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};