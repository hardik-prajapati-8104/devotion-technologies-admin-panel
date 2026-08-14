<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;   // <-- Add this line
use App\Models\Client;
use App\Models\HomeBanner;
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
        return view('frontend.index', compact('banners', 'clients'));
    }
}