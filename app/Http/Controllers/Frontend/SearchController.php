<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
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

        // Match services by their own name OR their category's name,
        // so searching "Deep Cleaning" (a category) still surfaces its services.
        $services = Service::query()
            ->with('category:id,name,slug')
            ->where('status', 1)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%'.$q.'%')
                      ->orWhereHas('category', function ($catQuery) use ($q) {
                          $catQuery->where('name', 'like', '%'.$q.'%');
                      });
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'slug', 'service_category_id', 'featured_image']);

        return response()->json([
            'categories' => $categories,
            'services'   => $services->map(fn ($s) => [
                'id'             => $s->id,
                'name'           => $s->name,
                'slug'           => $s->slug,
                'featured_image' => $s->featured_image,
                'category_slug'  => $s->category->slug ?? null,
            ]),
        ]);
    }
}