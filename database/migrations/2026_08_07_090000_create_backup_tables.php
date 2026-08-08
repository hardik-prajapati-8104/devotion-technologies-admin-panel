<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['database', 'files', 'full']);
            $table->string('disk')->default('local');
            $table->string('filename');
            $table->unsignedBigInteger('size')->nullable(); // bytes
            $table->enum('status', ['running', 'completed', 'failed'])->default('running');
            $table->text('error_message')->nullable();
            $table->enum('source', ['manual', 'scheduled'])->default('manual');
            $table->foreignId('triggered_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Single-row settings table — same pattern as admin_security_settings.
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('schedule_enabled')->default(false);
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->string('time', 5)->default('02:00'); // 24h "HH:mm", server time
            $table->unsignedInteger('retention_days')->default(30); // 0 = keep forever
            $table->boolean('backup_database')->default(true);
            $table->boolean('backup_files')->default(true);
            $table->text('file_paths')->nullable(); // one relative path per line, e.g. storage/app/public
            $table->string('notify_email')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
        Schema::dropIfExists('backups');
    }
};
