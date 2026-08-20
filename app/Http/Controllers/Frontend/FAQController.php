<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;   // <-- Add this line
use App\Models\Faq;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    /**
     * Display the Coming Soon page.
     */
    public function index()
    {
         $faqs = Faq::active()->ordered()->get();
        // $faqs = Faq::active()->ordered()->get()->groupBy('faq_category_id');
        // $categories = \App\Models\FaqCategory::whereIn('id', $faqs->keys())->get();
        return view('frontend.faq', compact('faqs'));
    }
}