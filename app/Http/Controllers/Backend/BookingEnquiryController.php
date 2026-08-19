<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\NewBookingEnquiryMail;
use App\Models\BookingEnquiry;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingEnquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:150',
            'phone'               => 'required|string|max:30',
            'email'               => 'required|email|max:150',
            'service_category_id' => 'required|integer|exists:service_categories,id',
            'address'             => 'required|string|max:500',
            'description'         => 'nullable|string|max:2000',
        ], [
            'service_category_id.required' => 'Please select a service.',
            'service_category_id.exists'   => 'Please select a valid service.',
        ]);

        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr((string) $request->userAgent(), 0, 255);

        // Save enquiry first
        $enquiry = BookingEnquiry::create($data);

        /*
         * Get notification email dynamically from settings table.
         * Fallback email is used if the setting doesn't exist.
         */
        $notifyEmail = Setting::get(
            'booking_notification_email',
            'hardikprajapati8104@gmail.com'
        );

        // Send notification email
        try {
            if ($notifyEmail && filter_var($notifyEmail, FILTER_VALIDATE_EMAIL)) {

                Mail::to($notifyEmail)
                    ->send(new NewBookingEnquiryMail($enquiry));

                $enquiry->update([
                    'email_sent' => true
                ]);
            } else {
                Log::warning('Invalid booking notification email configured.', [
                    'enquiry_id' => $enquiry->id,
                    'email'      => $notifyEmail,
                ]);
            }

        } catch (\Throwable $e) {

            Log::error('Booking enquiry email failed to send.', [
                'enquiry_id' => $enquiry->id,
                'email'      => $notifyEmail,
                'error'      => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thanks! Your booking request has been received. We will contact you shortly.',
        ]);
    }
}