<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Universal Recycle Bin metadata.
     *
     * One record is created for each recyclable/soft-deleted model.
     *
     * Examples:
     * - Country
     * - Task
     * - Announcement
     * - Notice
     * - Service
     * - Project
     * - Blog
     * - Any other model using SoftDeletes
     */
    public function up(): void
    {
        Schema::create('recycle_bin_meta', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Recyclable Polymorphic Relation
            |--------------------------------------------------------------------------
            |
            | We intentionally use 191 instead of morphs() because older MySQL
            | installations with utf8mb4 can have a 1000-byte index limit.
            |
            | recyclable_type = App\Models\Service
            | recyclable_id   = 15
            |
            */

            $table->string('recyclable_type', 191);

            $table->unsignedBigInteger('recyclable_id');

            /*
            |--------------------------------------------------------------------------
            | Recycle Bin Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'trashed',
                'archived',
            ])->default('trashed');

            /*
            |--------------------------------------------------------------------------
            | Deleted Information
            |--------------------------------------------------------------------------
            */

            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->timestamp('deleted_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Archived Information
            |--------------------------------------------------------------------------
            |
            | Archiving pauses the automatic deletion countdown.
            |
            */

            $table->foreignId('archived_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->timestamp('archived_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Automatic Permanent Deletion
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | deleted_at      = 2026-08-08 10:00:00
            | auto_delete_at  = 2026-09-07 10:00:00
            |
            | If the record is archived, auto_delete_at can be set to NULL.
            |
            */

            $table->timestamp('auto_delete_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            // One recycle-bin metadata record per model.
            $table->unique(
                ['recyclable_type', 'recyclable_id'],
                'recycle_bin_recyclable_unique'
            );

            // Faster filtering by status.
            $table->index(
                'status',
                'recycle_bin_status_index'
            );

            // Faster automatic cleanup queries.
            $table->index(
                'auto_delete_at',
                'recycle_bin_auto_delete_at_index'
            );

            // Faster deleted-record listing.
            $table->index(
                'deleted_at',
                'recycle_bin_deleted_at_index'
            );

            // Useful for admin activity/history.
            $table->index(
                'deleted_by',
                'recycle_bin_deleted_by_index'
            );

            $table->index(
                'archived_by',
                'recycle_bin_archived_by_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recycle_bin_meta');
    }
};