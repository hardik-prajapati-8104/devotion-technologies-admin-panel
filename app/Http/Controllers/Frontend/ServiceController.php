<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;   // <-- Add this line
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display the Coming Soon page.
     */
    public function index()
    {
        $services = Service::where('status', 1)->orderBy('display_order')->get();
        return view('frontend.services', compact('services'));
    }

      public function show(string $slug)
    {
        $service = Service::where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $categories = ServiceCategory::where('status', 1)->orderBy('name')->get();
 
        // Related services: same category, excluding this one, active only.
        $relatedServices = Service::where('status', 1)
            ->where('id', '!=', $service->id)
            ->when($service->service_category_id, fn ($q) => $q->where('service_category_id', $service->service_category_id))
            ->orderBy('display_order')
            ->limit(3)
            ->get();
 
        return view('frontend.service-show', compact('service', 'relatedServices', 'categories'));
    }
}