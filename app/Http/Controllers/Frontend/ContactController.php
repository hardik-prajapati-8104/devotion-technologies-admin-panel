<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;   // <-- Add this line
use App\Mail\ContactEnquiryReceived;
use App\Models\ContactEnquiry;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Display the Coming Soon page.
     */
    public function index()
    {
        return view('frontend.contact');
    }

     public function store(Request $request)
        {
            $validated = $request->validate([
                'name'     => 'required|string|max:150',
                'email'    => 'required|email|max:150',
                'services' => 'required|string|max:150',
                'message'  => 'required|string|max:2000',
            ]);

            $enquiry = ContactEnquiry::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'phone'      => null,
                'subject'    => $validated['services'],
                'message'    => $validated['message'],
                'source'     => 'website_contact_form',
                'ip_address' => $request->ip(),
            ]);

            $notifyEmail = Setting::where('key', 'booking_notification_email')->value('value');

            if ($notifyEmail) {
                try {
                    Mail::to($notifyEmail)->send(new ContactEnquiryReceived($enquiry));
                } catch (\Exception $e) {
                    // Don't fail the whole request just because the email didn't send —
                    // the enquiry is already saved in the DB either way.
                    Log::error('Contact enquiry email failed: ' . $e->getMessage());
                }
            }

            return response()->json(['success' => true]);
        }
 
}