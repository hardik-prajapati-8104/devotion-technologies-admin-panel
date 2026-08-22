<?php

use App\Models\SeoSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds everything needed to let admins create SEO records for pages
 * that aren't in the hard-coded DEFAULT_PAGES list, by giving every
 * row a page_url (the front-end path it applies to) and keeping the
 * is_default flag you already added, so the app can tell "always
 * exists / can't be deleted" pages apart from custom ones.
 *
 * Written defensively with hasColumn() checks so it's safe to run
 * even though `is_default` already exists in your database.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('seo_settings', 'page_url')) {
                $table->string('page_url')->nullable()->after('page_label');
            }
            if (! Schema::hasColumn('seo_settings', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('page_url');
            }
        });

        // Backfill page_url + is_default for the 7 pages that were being
        // seeded from the old hard-coded DEFAULT_PAGES array, matched by
        // their page_key.
        foreach (SeoSetting::DEFAULT_PAGES as $key => $meta) {
            DB::table('seo_settings')
                ->where('page_key', $key)
                ->update([
                    'page_url'   => $meta['url'],
                    'is_default' => true,
                ]);
        }

        // Any row that still has no page_url (custom pages created before
        // this migration, or any other edge case) gets a safe fallback
        // derived from its page_key, so the later unique index doesn't
        // choke on empty duplicates.
        DB::table('seo_settings')
            ->where(function ($q) {
                $q->whereNull('page_url')->orWhere('page_url', '');
            })
            ->get()
            ->each(function ($row) {
                DB::table('seo_settings')->where('id', $row->id)->update([
                    'page_url' => '/'.trim((string) $row->page_key, '/'),
                ]);
            });

        Schema::table('seo_settings', function (Blueprint $table) {
            if (! $this->indexExists('seo_settings', 'seo_settings_page_url_unique')) {
                $table->unique('page_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            if ($this->indexExists('seo_settings', 'seo_settings_page_url_unique')) {
                $table->dropUnique('seo_settings_page_url_unique');
            }
            if (Schema::hasColumn('seo_settings', 'page_url')) {
                $table->dropColumn('page_url');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($result) > 0;
    }
};
