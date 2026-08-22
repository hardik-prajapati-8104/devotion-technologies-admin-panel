<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add the column if it doesn't already exist
        if (! Schema::hasColumn('seo_settings', 'is_default')) {
            Schema::table('seo_settings', function (Blueprint $table) {
                $table->boolean('is_default')->default(false)->after('page_label');
            });
        }

        // Only add the unique index if it doesn't already exist
        $indexExists = collect(DB::select("SHOW INDEX FROM seo_settings WHERE Key_name = 'seo_settings_page_key_unique'"))->isNotEmpty();

        if (! $indexExists) {
            Schema::table('seo_settings', function (Blueprint $table) {
                $table->unique('page_key');
            });
        }

        // Backfill: mark every page currently matching the hardcoded
        // DEFAULT_PAGES list as a system default.
        $defaultKeys = array_keys(\App\Models\SeoSetting::DEFAULT_PAGES);
        DB::table('seo_settings')->whereIn('page_key', $defaultKeys)->update(['is_default' => true]);
    }

    public function down(): void
    {
        $indexExists = collect(DB::select("SHOW INDEX FROM seo_settings WHERE Key_name = 'seo_settings_page_key_unique'"))->isNotEmpty();

        if ($indexExists) {
            Schema::table('seo_settings', function (Blueprint $table) {
                $table->dropUnique('seo_settings_page_key_unique');
            });
        }

        if (Schema::hasColumn('seo_settings', 'is_default')) {
            Schema::table('seo_settings', function (Blueprint $table) {
                $table->dropColumn('is_default');
            });
        }
    }
};