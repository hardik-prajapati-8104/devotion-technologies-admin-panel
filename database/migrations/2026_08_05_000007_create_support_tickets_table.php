<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();

            // Ticket Number
            $table->string('ticket_number', 30)->unique(); // TCK-000001

            // Basic Details
            $table->string('subject');
            $table->longText('description');

            // Status
            $table->enum('status', [
                'open',
                'in_progress',
                'on_hold',
                'resolved',
                'closed',
                'reopened'
            ])->default('open');

            // Priority
            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent'
            ])->default('medium');

            // Category
            $table->string('category')->nullable();

            // Requester
            $table->foreignId('requester_admin_id')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->string('requester_name')->nullable();
            $table->string('requester_email')->nullable();
            $table->string('requester_phone')->nullable();

            // Assignment
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            // Created By
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            // Dates
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // Optional internal note
            $table->text('internal_note')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('priority');
            $table->index('assigned_to');
            $table->index('requester_admin_id');
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};