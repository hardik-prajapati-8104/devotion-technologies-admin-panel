<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_category_id')
                ->nullable()
                ->constrained('service_categories')
                ->nullOnDelete();

            $table->string('name', 191);

            // Unique indexed column
            $table->string('slug', 191)->unique();

            $table->string('short_description', 500)->nullable();

            $table->longText('full_description')->nullable();

            $table->string('featured_image', 191)->nullable();

            $table->string('icon', 191)->nullable(); // Bootstrap Icons class

            $table->unsignedInteger('display_order')->default(0);

            $table->boolean('is_featured')->default(false);

            $table->boolean('status')->default(true);

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
        Schema::dropIfExists('services');
    }
};