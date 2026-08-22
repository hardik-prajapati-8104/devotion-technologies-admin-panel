<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AiCitationLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['source', 'type', 'url_path', 'user_agent', 'referrer', 'ip', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Visit counts grouped by source (e.g. "GPTBot" => 42), for the
     * last N days, newest activity first.
     */
    public static function countsBySource(int $days = 30): \Illuminate\Support\Collection
    {
        return static::where('created_at', '>=', Carbon::now()->subDays($days))
            ->selectRaw('source, type, COUNT(*) as total')
            ->groupBy('source', 'type')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Most-visited pages by AI crawlers/referrals in the last N days.
     */
    public static function topPages(int $days = 30, int $limit = 10): \Illuminate\Support\Collection
    {
        return static::where('created_at', '>=', Carbon::now()->subDays($days))
            ->selectRaw('url_path, COUNT(*) as total')
            ->groupBy('url_path')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }
}
