<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;   // <-- Add this line
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display the list of clients.
     */
    public function index()
    {
        return view('frontend.our-clients');
    }
}