<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_category_id')
                ->nullable()
                ->constrained('project_categories')
                ->nullOnDelete();

            $table->string('name', 191);

            // Unique indexed column
            $table->string('slug', 191)->unique();

            $table->string('client_name', 191)->nullable();

            $table->string('location', 191)->nullable();

            $table->string('short_description', 500)->nullable();

            $table->longText('full_description')->nullable();

            $table->string('featured_image', 191)->nullable();

            $table->string('project_url', 191)->nullable();

            $table->string('technologies', 500)->nullable();

            $table->date('completion_date')->nullable();

            $table->enum('status', [
                'draft',
                'in_progress',
                'completed',
                'archived'
            ])->default('draft');

            $table->boolean('is_featured')->default(false);

            $table->unsignedInteger('display_order')->default(0);

            $table->string('seo_title', 191)->nullable();

            $table->string('meta_description', 500)->nullable();

            $table->string('og_image', 191)->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index(['status', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};