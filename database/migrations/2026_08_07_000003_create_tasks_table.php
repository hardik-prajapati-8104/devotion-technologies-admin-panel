<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained('task_boards')->cascadeOnDelete();
            $table->foreignId('column_id')->constrained('task_columns')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->date('due_date')->nullable();
            $table->unsignedInteger('position')->default(0); // order within its column
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('admins')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['column_id', 'position']);
        });

        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_id', 'admin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_assignees');
        Schema::dropIfExists('tasks');
    }
};
