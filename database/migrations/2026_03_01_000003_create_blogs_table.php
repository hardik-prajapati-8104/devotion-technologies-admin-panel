<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blog_category_id')
                ->nullable()
                ->constrained('blog_categories')
                ->nullOnDelete();

            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->string('title', 191);

            // Unique indexed column
            $table->string('slug', 191)->unique();

            $table->string('short_description', 500)->nullable();

            $table->longText('content')->nullable();

            $table->string('featured_image', 191)->nullable();

            $table->string('thumbnail', 191)->nullable();

            $table->unsignedSmallInteger('reading_time')->nullable();

            $table->timestamp('publish_date')->nullable();

            $table->enum('status', [
                'draft',
                'published',
                'scheduled'
            ])->default('draft');

            $table->boolean('is_featured')->default(false);

            // SEO
            $table->string('seo_title', 191)->nullable();

            $table->string('meta_description', 500)->nullable();

            $table->string('focus_keyword', 191)->nullable();

            $table->string('canonical_url', 500)->nullable();

            $table->string('og_title', 191)->nullable();

            $table->string('og_description', 500)->nullable();

            $table->string('og_image', 191)->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index(['status', 'publish_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};