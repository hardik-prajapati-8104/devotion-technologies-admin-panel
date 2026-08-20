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
    
}