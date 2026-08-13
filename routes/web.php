<?php

use App\Http\Controllers\Backend\MessageController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\BrochureController;
use App\Http\Controllers\Frontend\ClientController;
use App\Http\Controllers\Frontend\ComingSoonController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\CookiePolicyController;
use App\Http\Controllers\Frontend\FAQController;
use App\Http\Controllers\Frontend\GalleryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\SettingPageController;
use App\Http\Controllers\Frontend\PrivacyPolicyController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\TermConditionController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return 'Devotion Technology is working!';
// });

// Route::get('/', [ComingSoonController::class, 'index'])->name('coming-soon');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/terms-condition', [TermConditionController::class, 'index'])->name('terms-condition');
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
Route::get('/cookie-policy', [CookiePolicyController::class, 'index'])->name('cookie-policy');
Route::get('/our-clients', [ClientController::class, 'index'])->name('clients');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
Route::get('/booking', [GalleryController::class, 'index'])->name('booking');
Route::get('/faqs', [FAQController::class, 'index'])->name('faqs');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/brochure', [BrochureController::class, 'index'])->name('brochure');
Route::get('/settings', [SettingPageController::class, 'index'])->name('settings');
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
Route::get('/services', [ServiceController::class, 'index'])->name('services');

Route::prefix('admin/messages')->name('admin.messages.')->middleware(['auth:admin'])->group(function () {
    Route::get('/', [MessageController::class, 'index'])->name('index');
    Route::post('/{conversation}/send', [MessageController::class, 'send'])->name('send');

    // New:
    Route::post('/{conversation}/poll', [MessageController::class, 'poll'])->name('poll');
    Route::post('/poll/{message}/vote', [MessageController::class, 'pollVote'])->name('poll.vote');
    Route::post('/{conversation}/contact', [MessageController::class, 'contact'])->name('contact');
    Route::post('/{conversation}/event', [MessageController::class, 'event'])->name('event');
});