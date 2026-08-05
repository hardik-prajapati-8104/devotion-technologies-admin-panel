<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store read receipts for announcements, notices, and any other
     * readable models using a polymorphic relationship.
     */
    public function up(): void
    {
        // Prevent creating the table twice
        if (Schema::hasTable('read_receipts')) {
            return;
        }

        Schema::create('read_receipts', function (Blueprint $table) {
            $table->id();

            $table->string('readable_type', 100);
            $table->unsignedBigInteger('readable_id');
            $table->index(['readable_type', 'readable_id']);

            $table->foreignId('admin_id')
                ->constrained('admins')
                ->cascadeOnDelete();

            $table->timestamp('read_at')->useCurrent();

            $table->timestamps();

            $table->unique(
                ['readable_type', 'readable_id', 'admin_id'],
                'read_receipts_unique'
            );

            $table->index('admin_id');
            $table->index('read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('read_receipts');
    }
};