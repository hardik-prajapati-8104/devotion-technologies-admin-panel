<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();

            // Unique indexed column
            $table->string('email', 191)->unique();

            $table->string('name', 191)->nullable();

            $table->boolean('status')->default(true); // 1 = Active, 0 = Unsubscribed

            $table->timestamp('subscribed_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};