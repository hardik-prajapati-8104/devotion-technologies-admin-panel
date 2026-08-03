<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();

            $table->string('name', 191);

            // Unique indexed column
            $table->string('slug', 191)->unique();

            $table->text('description')->nullable();

            $table->boolean('status')->default(true);

            $table->string('seo_title', 191)->nullable();

            // Not indexed, so 500 characters is fine
            $table->string('meta_description', 500)->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};