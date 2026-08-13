<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;   // <-- Add this line
use Illuminate\Http\Request;

class TermConditionController extends Controller
{
    /**
     * Display the Coming Soon page.
     */
    public function index()
    {
        return view('frontend.terms-condition');
    }
}