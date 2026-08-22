<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Singleton settings row (always id = 1). Read with
 * AiVisibilitySetting::current() instead of querying directly, so
 * there's exactly one source of truth even if the seed row is ever
 * missing (it self-heals via firstOrCreate).
 */
class AiVisibilitySetting extends Model
{
    protected $table = 'ai_visibility_settings';

    protected $fillable = [
        'aeo_enabled', 'geo_enabled',
        'llm_crawlers_allowed', 'llm_training_policy',
        'llms_txt_enabled', 'llms_txt_intro',
        'robots_txt_ai_rules', 'default_schema_org_context',
        'citation_tracking_enabled',
    ];

    protected $casts = [
        'aeo_enabled'                => 'boolean',
        'geo_enabled'                 => 'boolean',
        'llms_txt_enabled'            => 'boolean',
        'citation_tracking_enabled'   => 'boolean',
        'llm_crawlers_allowed'        => 'array',
    ];

    public const TRAINING_POLICIES = ['allow', 'disallow', 'selective'];

    /**
     * The default list of AI crawler user-agents this package knows
     * about, used the first time the settings row is created.
     */
    public const DEFAULT_CRAWLERS = [
        'GPTBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-Web',
        'PerplexityBot', 'Google-Extended', 'CCBot', 'Bytespider', 'Amazonbot',
    ];

    /**
     * Referrer domains treated as "an AI product sent this visitor",
     * for the referral half of the citation log.
     */
    public const AI_REFERRER_DOMAINS = [
        'chat.openai.com', 'chatgpt.com', 'perplexity.ai',
        'claude.ai', 'gemini.google.com', 'copilot.microsoft.com', 'you.com',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'aeo_enabled'                => true,
            'geo_enabled'                => true,
            'llm_crawlers_allowed'       => self::DEFAULT_CRAWLERS,
            'llm_training_policy'        => 'selective',
            'llms_txt_enabled'           => true,
            'default_schema_org_context' => 'https://schema.org',
            'citation_tracking_enabled'  => true,
        ]);
    }
}
