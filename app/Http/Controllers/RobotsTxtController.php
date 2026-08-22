<?php

namespace App\Http\Controllers;

use App\Services\RobotsTxtGeneratorService;
use Illuminate\Http\Response;

/**
 * OPTIONAL drop-in. Only wire up the /robots.txt route to this
 * controller if your project doesn't already generate robots.txt
 * dynamically somewhere else. If it does, just call
 * RobotsTxtGeneratorService::buildAiBlock() from your existing
 * controller/route and append its output to what you already build —
 * don't run two things on the same route.
 */
class RobotsTxtController extends Controller
{
    public function __invoke(RobotsTxtGeneratorService $generator): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            '',
            'Sitemap: '.url('/sitemap.xml'),
            '',
            $generator->buildAiBlock(),
        ];

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
