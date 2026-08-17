<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    /**
     * AJAX endpoint: returns matching categories and services as JSON.
     * Triggers only when the query is at least 2 characters.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['categories' => [], 'services' => []]);
        }

        $categories = ServiceCategory::query()
            ->where('status', 1)
            ->where('name', 'like', '%'.$q.'%')
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'slug']);

        $services = Service::query()
            ->where('status', 1)
            ->where('name', 'like', '%'.$q.'%')
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'slug', 'service_category_id', 'featured_image']);

        return response()->json([
            'categories' => $categories,
            'services'   => $services,
        ]);
    }
}
