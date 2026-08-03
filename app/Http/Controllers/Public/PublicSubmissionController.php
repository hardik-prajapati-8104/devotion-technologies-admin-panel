<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreApplicationRequest;
use App\Http\Requests\Public\StoreEnquiryRequest;
use App\Http\Requests\Public\StoreSubscriberRequest;
use App\Models\CareerApplication;
use App\Models\ContactEnquiry;
use App\Models\NewsletterSubscriber;

/**
 * Unauthenticated endpoints that the public Devotion Technology
 * website (built separately from this admin panel) submits its forms
 * to. Every method here is reachable by anyone on the internet, so
 * each request is:
 *   - validated server-side (see app/Http/Requests/Public/*)
 *   - protected by a honeypot field ("website") that real visitors
 *     never see or fill
 *   - rate-limited at the route level (see routes/web.php: `throttle:10,1`)
 *
 * None of this replaces a proper CAPTCHA if spam becomes a real
 * problem in production — swap in hCaptcha/Turnstile at that point.
 */
class PublicSubmissionController extends Controller
{
    public function storeApplication(StoreApplicationRequest $request)
    {
        $data = $request->validated();

        $resumePath = $request->file('resume')->store('careers/resumes', 'public');

        CareerApplication::create([
            'career_id'      => $data['career_id'] ?? null,
            'applicant_name' => $data['applicant_name'],
            'email'          => $data['email'],
            'phone'          => $data['phone'] ?? null,
            'resume_path'    => $resumePath,
            'cover_letter'   => $data['cover_letter'] ?? null,
            'status'         => 'new',
            'ip_address'     => $request->ip(),
        ]);

        return back()->with('success', 'Thank you — your application has been submitted!');
    }

    public function storeEnquiry(StoreEnquiryRequest $request)
    {
        $data = $request->validated();

        ContactEnquiry::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'subject'    => $data['subject'] ?? null,
            'message'    => $data['message'],
            'source'     => $data['source'] ?? 'Contact Page',
            'status'     => 'new',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Thank you for reaching out — we\'ll be in touch soon!');
    }

    public function storeSubscriber(StoreSubscriberRequest $request)
    {
        $data = $request->validated();

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $data['email']]);
        $subscriber->name = $data['name'] ?? $subscriber->name;
        $subscriber->status = 1;
        $subscriber->subscribed_at = $subscriber->subscribed_at ?? now();
        $subscriber->save();

        return back()->with('success', 'You\'re subscribed! Thanks for joining our newsletter.');
    }
}
