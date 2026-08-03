<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();

            $table->string('name', 191);

            // Indexed column
            $table->string('slug', 191)->unique();

            $table->text('description')->nullable();

            $table->boolean('status')->default(true);

            $table->string('seo_title', 191)->nullable();

            // 500 characters is fine because it is NOT indexed
            $table->string('meta_description', 500)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};