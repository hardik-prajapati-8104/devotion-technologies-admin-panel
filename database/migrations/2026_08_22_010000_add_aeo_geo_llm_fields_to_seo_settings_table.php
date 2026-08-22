<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds AEO (Answer Engine Optimization), GEO (Generative Engine
 * Optimization), and LLM-visibility fields to seo_settings, so every
 * page (system or custom) can carry the extra structured data AI
 * crawlers and answer engines look for, on top of the classic SEO
 * fields already there.
 *
 * Idempotent — safe to re-run.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            // --- Feature toggles, per page -----------------------------
            if (! Schema::hasColumn('seo_settings', 'enable_aeo')) {
                $table->boolean('enable_aeo')->default(true)->after('twitter_image');
            }
            if (! Schema::hasColumn('seo_settings', 'enable_geo')) {
                $table->boolean('enable_geo')->default(true)->after('enable_aeo');
            }

            // --- AEO: direct-answer content -----------------------------
            if (! Schema::hasColumn('seo_settings', 'primary_question')) {
                $table->string('primary_question', 255)->nullable()->after('enable_geo');
            }
            if (! Schema::hasColumn('seo_settings', 'answer_summary')) {
                $table->text('answer_summary')->nullable()->after('primary_question');
            }
            if (! Schema::hasColumn('seo_settings', 'key_takeaways')) {
                // JSON array of short bullet strings
                $table->json('key_takeaways')->nullable()->after('answer_summary');
            }
            if (! Schema::hasColumn('seo_settings', 'faq_items')) {
                // JSON array of {question, answer}
                $table->json('faq_items')->nullable()->after('key_takeaways');
            }
            if (! Schema::hasColumn('seo_settings', 'how_to_steps')) {
                // JSON array of {title, body, image}
                $table->json('how_to_steps')->nullable()->after('faq_items');
            }

            // --- GEO / LLM: entity + authority signals ------------------
            if (! Schema::hasColumn('seo_settings', 'llm_citation_allowed')) {
                $table->boolean('llm_citation_allowed')->default(true)->after('how_to_steps');
            }
            if (! Schema::hasColumn('seo_settings', 'llm_training_allowed')) {
                $table->boolean('llm_training_allowed')->default(true)->after('llm_citation_allowed');
            }
            if (! Schema::hasColumn('seo_settings', 'entity_type')) {
                $table->string('entity_type', 100)->nullable()->after('llm_training_allowed');
            }
            if (! Schema::hasColumn('seo_settings', 'entity_name')) {
                $table->string('entity_name', 255)->nullable()->after('entity_type');
            }
            if (! Schema::hasColumn('seo_settings', 'primary_topics')) {
                // JSON array of strings
                $table->json('primary_topics')->nullable()->after('entity_name');
            }
            if (! Schema::hasColumn('seo_settings', 'author_name')) {
                $table->string('author_name', 150)->nullable()->after('primary_topics');
            }
            if (! Schema::hasColumn('seo_settings', 'author_credentials')) {
                $table->string('author_credentials', 255)->nullable()->after('author_name');
            }
            if (! Schema::hasColumn('seo_settings', 'author_social_profiles')) {
                // JSON array of URLs
                $table->json('author_social_profiles')->nullable()->after('author_credentials');
            }
            if (! Schema::hasColumn('seo_settings', 'reviewed_by_name')) {
                $table->string('reviewed_by_name', 150)->nullable()->after('author_social_profiles');
            }
            if (! Schema::hasColumn('seo_settings', 'reviewed_by_credentials')) {
                $table->string('reviewed_by_credentials', 255)->nullable()->after('reviewed_by_name');
            }
            if (! Schema::hasColumn('seo_settings', 'sources_and_references')) {
                // JSON array of {title, url}
                $table->json('sources_and_references')->nullable()->after('reviewed_by_credentials');
            }
            if (! Schema::hasColumn('seo_settings', 'target_audience')) {
                $table->string('target_audience', 255)->nullable()->after('sources_and_references');
            }
            if (! Schema::hasColumn('seo_settings', 'geo_target_locations')) {
                // JSON array of strings
                $table->json('geo_target_locations')->nullable()->after('target_audience');
            }
            if (! Schema::hasColumn('seo_settings', 'content_quality_score')) {
                $table->unsignedTinyInteger('content_quality_score')->nullable()->after('geo_target_locations');
            }

            // --- Structured data -----------------------------------------
            if (! Schema::hasColumn('seo_settings', 'schema_type')) {
                $table->string('schema_type', 50)->default('Article')->after('content_quality_score');
            }
            if (! Schema::hasColumn('seo_settings', 'schema_override_json')) {
                $table->json('schema_override_json')->nullable()->after('schema_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $columns = [
                'enable_aeo', 'enable_geo', 'primary_question', 'answer_summary',
                'key_takeaways', 'faq_items', 'how_to_steps',
                'llm_citation_allowed', 'llm_training_allowed', 'entity_type', 'entity_name',
                'primary_topics', 'author_name', 'author_credentials', 'author_social_profiles',
                'reviewed_by_name', 'reviewed_by_credentials', 'sources_and_references',
                'target_audience', 'geo_target_locations', 'content_quality_score',
                'schema_type', 'schema_override_json',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('seo_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
