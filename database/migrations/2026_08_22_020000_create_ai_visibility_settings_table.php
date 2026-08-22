<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Site-wide AEO/GEO/LLM settings. This table is a singleton — always
 * exactly one row (id = 1) — read/written through App\Models\AiVisibilitySetting::current().
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_visibility_settings')) {
            Schema::create('ai_visibility_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('aeo_enabled')->default(true);
                $table->boolean('geo_enabled')->default(true);

                // JSON array of AI crawler user-agent strings this site
                // has an opinion about, e.g. ["GPTBot","ClaudeBot","PerplexityBot"].
                $table->json('llm_crawlers_allowed')->nullable();

                // allow    = let every listed bot train on the whole site
                // disallow = block every listed bot from training site-wide
                // selective = respect each page's llm_training_allowed flag
                $table->enum('llm_training_policy', ['allow', 'disallow', 'selective'])->default('selective');

                $table->boolean('llms_txt_enabled')->default(true);
                $table->text('llms_txt_intro')->nullable();

                // Optional raw text appended as-is to the generated AI
                // section of robots.txt, for anything the UI doesn't cover.
                $table->text('robots_txt_ai_rules')->nullable();

                $table->string('default_schema_org_context', 255)->default('https://schema.org');
                $table->boolean('citation_tracking_enabled')->default(false);

                $table->timestamps();
            });
        }

        if (! DB::table('ai_visibility_settings')->where('id', 1)->exists()) {
            DB::table('ai_visibility_settings')->insert([
                'id'                        => 1,
                'aeo_enabled'               => true,
                'geo_enabled'               => true,
                'llm_crawlers_allowed'      => json_encode([
                    'GPTBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-Web',
                    'PerplexityBot', 'Google-Extended', 'CCBot', 'Bytespider', 'Amazonbot',
                ]),
                'llm_training_policy'       => 'selective',
                'llms_txt_enabled'          => true,
                'llms_txt_intro'            => null,
                'robots_txt_ai_rules'       => null,
                'default_schema_org_context'=> 'https://schema.org',
                'citation_tracking_enabled' => true,
                'created_at'                => now(),
                'updated_at'                => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_visibility_settings');
    }
};
