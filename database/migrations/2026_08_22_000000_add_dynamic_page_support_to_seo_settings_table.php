<?php

use App\Models\SeoSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Add page_url if it does not exist
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasColumn('seo_settings', 'page_url')) {

            Schema::table('seo_settings', function (Blueprint $table) {
                $table->string('page_url', 191)
                    ->nullable()
                    ->after('page_label');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 2. IMPORTANT
        |--------------------------------------------------------------------------
        | The column may already exist as VARCHAR(255) because an earlier
        | migration partially succeeded before failing on the unique index.
        |
        | Force it to VARCHAR(191).
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE `seo_settings`
            MODIFY `page_url` VARCHAR(191) NULL
        ");

        /*
        |--------------------------------------------------------------------------
        | 3. Add is_default if it does not exist
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasColumn('seo_settings', 'is_default')) {

            Schema::table('seo_settings', function (Blueprint $table) {
                $table->boolean('is_default')
                    ->default(false)
                    ->after('page_url');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Backfill default pages
        |--------------------------------------------------------------------------
        */

        foreach (SeoSetting::DEFAULT_PAGES as $key => $meta) {

            DB::table('seo_settings')
                ->where('page_key', $key)
                ->update([
                    'page_url'   => mb_substr($meta['url'], 0, 191),
                    'is_default' => true,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Generate fallback URLs
        |--------------------------------------------------------------------------
        */

        DB::table('seo_settings')
            ->where(function ($query) {
                $query->whereNull('page_url')
                    ->orWhere('page_url', '');
            })
            ->get()
            ->each(function ($row) {

                $pageKey = trim((string) $row->page_key, '/');

                $pageUrl = '/' . $pageKey;

                $pageUrl = mb_substr($pageUrl, 0, 191);

                DB::table('seo_settings')
                    ->where('id', $row->id)
                    ->update([
                        'page_url' => $pageUrl,
                    ]);
            });

        /*
        |--------------------------------------------------------------------------
        | 6. Check for duplicate URLs
        |--------------------------------------------------------------------------
        */

        $duplicates = DB::table('seo_settings')
            ->select('page_url', DB::raw('COUNT(*) as total'))
            ->whereNotNull('page_url')
            ->groupBy('page_url')
            ->having('total', '>', 1)
            ->get();

        if ($duplicates->isNotEmpty()) {

            throw new RuntimeException(
                'Duplicate page_url values exist in seo_settings. ' .
                'Remove duplicates before creating the unique index: ' .
                $duplicates->pluck('page_url')->implode(', ')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Add unique index
        |--------------------------------------------------------------------------
        */

        $indexExists = DB::select(
            "SHOW INDEX FROM `seo_settings` WHERE Key_name = ?",
            ['seo_settings_page_url_unique']
        );

        if (empty($indexExists)) {

            Schema::table('seo_settings', function (Blueprint $table) {

                $table->unique(
                    'page_url',
                    'seo_settings_page_url_unique'
                );
            });
        }
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Remove unique index
        |--------------------------------------------------------------------------
        */

        $indexExists = DB::select(
            "SHOW INDEX FROM `seo_settings` WHERE Key_name = ?",
            ['seo_settings_page_url_unique']
        );

        if (!empty($indexExists)) {

            Schema::table('seo_settings', function (Blueprint $table) {

                $table->dropUnique(
                    'seo_settings_page_url_unique'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Remove is_default
        |--------------------------------------------------------------------------
        */

        if (Schema::hasColumn('seo_settings', 'is_default')) {

            Schema::table('seo_settings', function (Blueprint $table) {
                $table->dropColumn('is_default');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Remove page_url
        |--------------------------------------------------------------------------
        */

        if (Schema::hasColumn('seo_settings', 'page_url')) {

            Schema::table('seo_settings', function (Blueprint $table) {
                $table->dropColumn('page_url');
            });
        }
    }
};