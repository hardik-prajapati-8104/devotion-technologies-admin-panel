<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;   // <-- Add this line
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display the Coming Soon page.
     */
    public function index()
    {
        $serviceCategories = ServiceCategory::where('status', 1)->orderBy('name')->get();
        return view('frontend.booking', compact('serviceCategories'));
    }
}