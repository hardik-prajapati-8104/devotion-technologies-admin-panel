<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_boards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('#aa8038'); // board accent color
            $table->foreignId('created_by')->constrained('admins')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('task_board_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained('task_boards')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->boolean('is_owner')->default(false);
            $table->timestamps();

            $table->unique(['board_id', 'admin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_board_members');
        Schema::dropIfExists('task_boards');
    }
};
