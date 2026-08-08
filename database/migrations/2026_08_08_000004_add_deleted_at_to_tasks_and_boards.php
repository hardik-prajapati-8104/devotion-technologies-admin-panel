<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('task_boards', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('task_comments', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('task_attachments', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('task_checklists', fn (Blueprint $table) => $table->softDeletes());
    }

    public function down(): void
    {
        Schema::table('tasks', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('task_boards', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('task_comments', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('task_attachments', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('task_checklists', fn (Blueprint $table) => $table->dropSoftDeletes());
    }
};
