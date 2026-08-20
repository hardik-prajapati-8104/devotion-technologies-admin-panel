<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;   // <-- Add this line
use App\Models\Testimonial;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display the Coming Soon page.
     */
    public function index()
    {
         $reviews = Testimonial::active()->ordered()->get();
        return view('frontend.reviews', compact('reviews'));
    }
}