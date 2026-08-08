<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained('task_boards')->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 20)->default('#6c757d');
            $table->timestamps();
        });

        Schema::create('task_label_task', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('label_id')->constrained('task_labels')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_id', 'label_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_label_task');
        Schema::dropIfExists('task_labels');
    }
};
