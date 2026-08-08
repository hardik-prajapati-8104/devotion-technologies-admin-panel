<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Single-row global settings, plus optional per-model overrides
     * stored as JSON (e.g. {"App\\Models\\Task": 60} keeps tasks in
     * trash for 60 days even though the default is 30).
     */
    public function up(): void
    {
        Schema::create('recycle_bin_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('default_retention_days')->default(30);
            $table->json('retention_overrides')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recycle_bin_settings');
    }
};
