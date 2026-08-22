<?php

namespace App\Http\Controllers;

use App\Models\AiVisibilitySetting;
use App\Services\LlmsTxtGeneratorService;
use Illuminate\Http\Response;

class LlmsTxtController extends Controller
{
    public function __invoke(LlmsTxtGeneratorService $generator): Response
    {
        $settings = AiVisibilitySetting::current();

        if (! $settings->llms_txt_enabled) {
            abort(404);
        }

        return response($generator->generate(), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
