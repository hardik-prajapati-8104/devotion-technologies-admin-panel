<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id();

            $table->string('title', 191);

            // Unique indexed column
            $table->string('slug', 191)->unique();

            $table->string('department', 191)->nullable();

            $table->string('location', 191)->nullable();

            $table->enum('employment_type', [
                'full_time',
                'part_time',
                'internship',
                'contract',
                'remote'
            ])->default('full_time');

            $table->string('experience', 191)->nullable();

            $table->string('salary_info', 191)->nullable();

            $table->string('short_description', 500)->nullable();

            $table->longText('description')->nullable();

            $table->longText('responsibilities')->nullable();

            $table->longText('requirements')->nullable();

            $table->longText('benefits')->nullable();

            $table->date('application_deadline')->nullable();

            $table->boolean('status')->default(true);

            $table->boolean('is_featured')->default(false);

            $table->string('seo_title', 191)->nullable();

            $table->string('meta_description', 500)->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index(['status', 'application_deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};