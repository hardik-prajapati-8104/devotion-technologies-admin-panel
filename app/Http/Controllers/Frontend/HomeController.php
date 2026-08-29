<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;   // <-- Add this line
use App\Models\Client;
use App\Models\HomeBanner;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the Coming Soon page.
     */
    public function index()
    {
        $banners = HomeBanner::live()->get();
        $clients = Client::active()->ordered()->get();
        $categories = ServiceCategory::where('status', 1)->get();
        $services = Service::where('status', 1)->orderBy('display_order')->get();
        $testimonials = Testimonial::active()->ordered()->get();
        return view('frontend.index', compact('banners', 'clients', 'categories', 'services', 'testimonials'));
    }

       public function category(ServiceCategory $category)
    {
        // Only active categories can be viewed
        abort_if($category->status != 1, 404);

        // Get only active services of selected category
        $services = Service::query()
            ->where('status', 1)
            ->where('service_category_id', $category->id)
            ->orderBy('display_order')
            ->get();

        // Get all active categories for the category filter
        $categories = ServiceCategory::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('frontend.services', compact(
            'category',
            'categories',
            'services'
        ));
    }
    
}