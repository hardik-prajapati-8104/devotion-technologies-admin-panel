<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\NewBookingEnquiryMail;
use App\Models\BookingEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingEnquiryController extends Controller

{
    /**
     * Where the "New Booking Enquiry" notification email is sent.
     * Move this to config/mail.php or a Setting (see the Settings page
     * built earlier) if you want it editable from the admin panel later.
     */
    private const NOTIFY_EMAIL = 'hardikprajapati8104@gmail.com';

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name'            => 'required|string|max:150',
            'phone'                => 'required|string|max:30',
            'email'                => 'required|email|max:150',
            'service_category_id'  => 'required|integer|exists:service_categories,id',
            'address'               => 'required|string|max:500',
            'description'           => 'nullable|string|max:2000',
        ], [
            'service_category_id.required' => 'Please select a service.',
            'service_category_id.exists'   => 'Please select a valid service.',
        ]);

        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr((string) $request->userAgent(), 0, 255);

        // 1. Always save to the database first. The booking must never be
        //    "lost" just because the mail server is briefly unreachable.
        $enquiry = BookingEnquiry::create($data);

        // 2. Then try to email a notification. Failure here is logged but
        //    does not fail the request — the customer still gets a success
        //    response and the enquiry is safely in the database either way.
        try {
            Mail::to(self::NOTIFY_EMAIL)->send(new NewBookingEnquiryMail($enquiry));
            $enquiry->update(['email_sent' => true]);
        } catch (\Throwable $e) {
            Log::error('Booking enquiry email failed to send.', [
                'enquiry_id' => $enquiry->id,
                'error'      => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thanks! Your booking request has been received. We will contact you shortly.',
        ]);
    }
}
