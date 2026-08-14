<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;   // <-- Add this line
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display the list of clients.
     */
    public function index()
    {
        $clients = Client::active()->ordered()->get();
        return view('frontend.our-clients', compact('clients'));
    }
}