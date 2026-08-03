<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_menus', function (Blueprint $table) {
            $table->id();

            // Self-referencing parent for one level of submenus (matches current sidebar depth)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('admin_menus')
                ->nullOnDelete();

            // 'section'  = a plain group heading like "Website Content" (no link)
            // 'item'     = an actual nav link, may or may not have children
            $table->enum('type', ['section', 'item'])->default('item');

            $table->string('title');
            $table->string('icon')->nullable(); // bootstrap-icons class, e.g. "bi bi-grid-1x2-fill"

            // Laravel route name to link to, e.g. "admin.services.index". Null for group-only parents ("#").
            $table->string('route_name')->nullable();

            // Comma separated routeIs() patterns used to decide "active"/"open" state,
            // e.g. "admin.blogs*,admin.blog-categories*,admin.blog-tags*"
            $table->string('route_pattern')->nullable();

            // Spatie permission name required to see this item, e.g. "services.view". Null = always visible.
            $table->string('permission')->nullable();

            // Key used to resolve a dynamic count badge (see AdminMenuBadgeResolver). Null = no badge.
            $table->string('badge_key')->nullable();

            $table->boolean('open_in_new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_menus');
    }
};
