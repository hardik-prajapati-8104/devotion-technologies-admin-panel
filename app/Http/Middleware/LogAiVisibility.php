<?php

namespace App\Http\Middleware;

use App\Models\AiCitationLog;
use App\Models\AiVisibilitySetting;
use Closure;
use Illuminate\Http\Request;

/**
 * Lightweight AI-visibility logger. Register on the `web` middleware
 * group (or a subset of routes) — see routes/seo_routes_snippet.php.
 *
 * Detects two things:
 *   - A known LLM crawler UA fetching a page directly (source = bot name)
 *   - A human arriving with a Referer header from a known AI product
 *     (source = referrer domain, e.g. "chat.openai.com")
 *
 * Writes a row to ai_citation_logs for each. This does a synchronous
 * DB insert, which is fine for low/medium traffic; on a high-traffic
 * site, swap the insert for a queued job (see README).
 */
class LogAiVisibility
{
    public function handle(Request $request, Closure $next)
    {
        $settings = AiVisibilitySetting::current();

        if (! $settings->citation_tracking_enabled) {
            return $next($request);
        }

        $userAgent = (string) $request->userAgent();
        $referrer = (string) $request->headers->get('referer', '');

        $botMatch = $this->matchCrawler($userAgent, $settings->llm_crawlers_allowed ?: []);
        $referralMatch = $botMatch ? null : $this->matchReferrer($referrer);

        if ($botMatch || $referralMatch) {
            try {
                AiCitationLog::create([
                    'source'     => $botMatch ?: $referralMatch,
                    'type'       => $botMatch ? 'crawler' : 'referral',
                    'url_path'   => '/'.ltrim($request->path(), '/'),
                    'user_agent' => $userAgent ?: null,
                    'referrer'   => $referrer ?: null,
                    'ip'         => $request->ip(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Never let logging break the actual request.
                report($e);
            }
        }

        return $next($request);
    }

    private function matchCrawler(string $userAgent, array $bots): ?string
    {
        if ($userAgent === '') {
            return null;
        }

        foreach ($bots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return $bot;
            }
        }

        return null;
    }

    private function matchReferrer(string $referrer): ?string
    {
        if ($referrer === '') {
            return null;
        }

        $host = parse_url($referrer, PHP_URL_HOST) ?: '';

        foreach (AiVisibilitySetting::AI_REFERRER_DOMAINS as $domain) {
            if ($host === $domain || str_ends_with($host, '.'.$domain)) {
                return $domain;
            }
        }

        return null;
    }
}
