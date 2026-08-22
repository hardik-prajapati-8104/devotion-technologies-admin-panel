<?php

namespace App\Services;

use App\Models\SeoSetting;
use Illuminate\Http\Request;

class SeoResolverService
{
    /**
     * Resolve the best-matching SeoSetting for the current request, or
     * null if nothing has been configured for this URL yet.
     */
    public function resolve(Request $request): ?SeoSetting
    {
        return SeoSetting::forUrl($request->path());
    }
}
