<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->enum('status', ['draft', 'published'])->default('draft');
            // Targeted audience: null = everyone, otherwise a JSON list of
            // Spatie role names (e.g. ["editor","content-manager"]) so a
            // notice can be aimed at a specific team/department.
            $table->json('target_roles')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->constrained('admins')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
