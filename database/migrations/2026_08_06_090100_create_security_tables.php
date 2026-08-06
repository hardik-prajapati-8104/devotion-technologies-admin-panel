<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin Security Settings
        |--------------------------------------------------------------------------
        */

        Schema::create('admin_security_settings', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Two Factor Authentication
            |--------------------------------------------------------------------------
            */

            $table->boolean('two_factor_required')->default(false);

            /*
            |--------------------------------------------------------------------------
            | Session
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('session_timeout_minutes')->default(30);

            /*
            |--------------------------------------------------------------------------
            | Login Attempts
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('max_login_attempts')->default(5);
            $table->unsignedTinyInteger('lockout_minutes')->default(15);

            /*
            |--------------------------------------------------------------------------
            | IP Rules
            |--------------------------------------------------------------------------
            */

            $table->boolean('ip_rules_enabled')->default(false);

            /*
            |--------------------------------------------------------------------------
            | Google reCAPTCHA
            |--------------------------------------------------------------------------
            */

            $table->boolean('recaptcha_enabled')->default(false);
            $table->string('recaptcha_site_key')->nullable();
            $table->text('recaptcha_secret_key')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Password Policy
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('password_min_length')->default(8);

            $table->boolean('password_require_upper')->default(true);
            $table->boolean('password_require_lower')->default(true);
            $table->boolean('password_require_number')->default(true);
            $table->boolean('password_require_symbol')->default(true);

            $table->unsignedSmallInteger('password_expiry_days')->default(90);

            $table->boolean('force_password_change_default')->default(false);

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Admin IP Rules
        |--------------------------------------------------------------------------
        */

        Schema::create('admin_ip_rules', function (Blueprint $table) {

            $table->id();

            $table->ipAddress('ip');

            $table->enum('type', [
                'whitelist',
                'blacklist'
            ]);

            $table->string('note')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['ip','type']);

            $table->index('type');
        });

        /*
        |--------------------------------------------------------------------------
        | Admin Login Attempts
        |--------------------------------------------------------------------------
        */

        Schema::create('admin_login_attempts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->string('email', 191)->nullable();

            $table->ipAddress('ip');

            $table->text('user_agent')->nullable();

            $table->boolean('successful')->default(false);

            $table->string('reason',191)->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index(['email', 'created_at']);
            $table->index(['ip', 'created_at']);
            $table->index('successful');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_login_attempts');
        Schema::dropIfExists('admin_ip_rules');
        Schema::dropIfExists('admin_security_settings');
    }
};