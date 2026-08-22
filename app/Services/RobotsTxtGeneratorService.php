<?php

namespace App\Services;

use App\Models\AiVisibilitySetting;
use App\Models\SeoSetting;

/**
 * Builds the AI-crawler block of robots.txt: standard "Allow/Disallow: /"
 * per bot based on the site's training policy, plus per-page Disallow
 * rules for any page that opted out of LLM training individually (only
 * meaningful when the policy is 'selective').
 *
 * This only returns the AI-specific block — merge it into whatever your
 * existing robots.txt generation already outputs for normal crawlers,
 * or use RobotsTxtController as a full drop-in replacement if you don't
 * have one yet. See routes/seo_routes_snippet.php.
 */
class RobotsTxtGeneratorService
{
    public function buildAiBlock(): string
    {
        $settings = AiVisibilitySetting::current();
        $bots = $settings->llm_crawlers_allowed ?: [];

        if (empty($bots)) {
            return '';
        }

        $lines = ['# --- AI / LLM crawlers ---'];

        foreach ($bots as $bot) {
            $lines[] = "User-agent: {$bot}";

            if ($settings->llm_training_policy === 'disallow') {
                $lines[] = 'Disallow: /';
            } elseif ($settings->llm_training_policy === 'allow') {
                $lines[] = 'Allow: /';
            } else {
                // selective: allow the whole site by default, then list
                // the specific paths that opted out individually.
                $lines[] = 'Allow: /';
                foreach ($this->optedOutPaths() as $path) {
                    $lines[] = "Disallow: {$path}";
                }
            }

            $lines[] = '';
        }

        if ($settings->robots_txt_ai_rules) {
            $lines[] = trim($settings->robots_txt_ai_rules);
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    private function optedOutPaths(): array
    {
        return SeoSetting::where('llm_training_allowed', false)
            ->pluck('page_url')
            ->all();
    }
}
