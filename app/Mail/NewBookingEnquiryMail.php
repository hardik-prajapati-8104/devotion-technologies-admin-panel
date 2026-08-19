<?php

namespace App\Mail;

use App\Models\BookingEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBookingEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BookingEnquiry $enquiry)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Booking Enquiry — ' . $this->enquiry->full_name,
            replyTo: [$this->enquiry->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'frontend.emails.booking-enquiry',
        );
    }
}
