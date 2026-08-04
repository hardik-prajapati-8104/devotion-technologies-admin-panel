<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 5)->unique();          // e.g. IN, US, AE
            $table->string('phone_code', 10)->nullable();  // e.g. +91
            $table->string('flag')->nullable();             // media library URL
            $table->boolean('status')->default(true);      // 1 = active, 0 = inactive
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
