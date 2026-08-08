<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained('task_boards')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('position')->default(0);
            $table->string('color')->default('#6c757d');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_columns');
    }
};
