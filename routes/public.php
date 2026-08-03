<?php

use App\Http\Controllers\Public\PublicSubmissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Form Routes (Devotion Technology public website)
|--------------------------------------------------------------------------
| These are the endpoints the *public-facing* website (built separately
| from this admin panel) POSTs its forms to. Unlike routes/admin.php,
| nothing here requires authentication — these are hit by anonymous
| visitors, so every route is rate-limited and every request is
| validated + honeypot-checked in its Form Request class
| (app/Http/Requests/Public/*).
|
| Registered in routes/web.php with:
|   require __DIR__.'/public.php';
*/

Route::middleware('throttle:10,1')->group(function () {
    Route::post('careers/apply', [PublicSubmissionController::class, 'storeApplication'])
        ->name('public.careers.apply');

    Route::post('contact', [PublicSubmissionController::class, 'storeEnquiry'])
        ->name('public.contact.store');

    Route::post('newsletter/subscribe', [PublicSubmissionController::class, 'storeSubscriber'])
        ->name('public.newsletter.subscribe');
});
