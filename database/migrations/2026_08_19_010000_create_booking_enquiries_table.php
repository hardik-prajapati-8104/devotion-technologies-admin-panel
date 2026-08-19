<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone', 30);
            $table->string('email');
            $table->foreignId('service_category_id')
                ->constrained('service_categories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('address', 500);
            $table->text('description')->nullable();

            // Lightweight status tracking so this can double as a mini CRM later.
            $table->enum('status', ['new', 'contacted', 'confirmed', 'completed', 'cancelled'])
                ->default('new');

            // Set true only after the notification email successfully sends,
            // so a mail-server hiccup never causes a submission to look "lost"
            // (the DB row is still saved even if the email fails).
            $table->boolean('email_sent')->default(false);

            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_enquiries');
    }
};
