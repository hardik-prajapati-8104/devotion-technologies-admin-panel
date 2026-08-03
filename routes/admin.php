<?php

use App\Http\Controllers\Backend\AdminMenuController;
use App\Http\Controllers\Backend\ActivityLogController;
use App\Http\Controllers\Backend\ApplicationController;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\BlogCategoryController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\BlogTagController;
use App\Http\Controllers\Backend\CareerController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EnquiryController;
use App\Http\Controllers\Backend\FaqCategoryController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\ProjectCategoryController;
use App\Http\Controllers\Backend\ProjectController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SeoController;
use App\Http\Controllers\Backend\ServiceCategoryController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SubscriberController;
use App\Http\Controllers\Backend\TeamController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes  (Devotion Technology admin panel)
|--------------------------------------------------------------------------
| Registered in routes/web.php with:
|   require __DIR__.'/admin.php';
|
| Everything lives under /admin and the "admin." route name prefix,
| completely separated from the public site routes.
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('web')
    ->group(function () {

        // Guest-only (not logged in) routes
        Route::middleware(['web', 'guest:admin'])->group(function () {
            Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
            Route::post('login', [LoginController::class, 'login'])
                ->middleware('throttle:10,1')
                ->name('login.submit');
        });

        // Authenticated admin routes
        Route::middleware(['web', 'auth:admin'])->group(function () {
            Route::post('logout', [LoginController::class, 'logout'])->name('logout');

            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // ----------------------------------------------------------
            // Phase 2 — Users, Roles & Permissions, Profile
            // ----------------------------------------------------------
            Route::resource('users', UserController::class)->except(['show']);
            Route::patch('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

            Route::resource('roles', RoleController::class)->except(['show']);

            Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::get('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
            Route::put('profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

            
            Route::post('menus/reorder', [AdminMenuController::class, 'reorder'])->name('menus.reorder');
            Route::resource('menus', AdminMenuController::class);


            // ----------------------------------------------------------
            // Phase 3 — Services & Projects (with categories)
            // ----------------------------------------------------------
            Route::resource('service-categories', ServiceCategoryController::class)->except(['show']);
            Route::delete('services/bulk-delete', [ServiceController::class, 'bulkDestroy'])->name('services.bulk-delete');
            Route::resource('services', ServiceController::class)->except(['show']);
            Route::patch('services/{id}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

            Route::resource('project-categories', ProjectCategoryController::class)->except(['show']);
            Route::resource('projects', ProjectController::class)->except(['show']);
            Route::patch('projects/{id}/toggle-featured', [ProjectController::class, 'toggleFeatured'])->name('projects.toggle-featured');
            Route::delete('projects/{project}/gallery/{image}', [ProjectController::class, 'destroyGalleryImage'])->name('projects.gallery.destroy');

            // ----------------------------------------------------------
            // Phase 4 — Blog Management
            // ----------------------------------------------------------
            Route::resource('blog-categories', BlogCategoryController::class)->except(['show']);

            Route::get('blog-tags', [BlogTagController::class, 'index'])->name('blog-tags.index');
            Route::delete('blog-tags/{id}', [BlogTagController::class, 'destroy'])->name('blog-tags.destroy');

            Route::resource('blogs', BlogController::class)->except(['show']);
            Route::get('blogs/{id}/preview', [BlogController::class, 'preview'])->name('blogs.preview');
            Route::post('blogs/{id}/duplicate', [BlogController::class, 'duplicate'])->name('blogs.duplicate');
            Route::patch('blogs/{id}/toggle-status', [BlogController::class, 'toggleStatus'])->name('blogs.toggle-status');

            // ----------------------------------------------------------
            // Phase 5 — Testimonials, FAQs, Team
            // ----------------------------------------------------------
            Route::post('testimonials/reorder', [TestimonialController::class, 'reorder'])->name('testimonials.reorder');
            Route::resource('testimonials', TestimonialController::class)->except(['show']);

            Route::get('faq-categories', [FaqCategoryController::class, 'index'])->name('faq-categories.index');
            Route::post('faq-categories', [FaqCategoryController::class, 'store'])->name('faq-categories.store');
            Route::put('faq-categories/{id}', [FaqCategoryController::class, 'update'])->name('faq-categories.update');
            Route::delete('faq-categories/{id}', [FaqCategoryController::class, 'destroy'])->name('faq-categories.destroy');

            Route::post('faqs/reorder', [FaqController::class, 'reorder'])->name('faqs.reorder');
            Route::resource('faqs', FaqController::class)->except(['show']);
            Route::patch('faqs/{id}/toggle-status', [FaqController::class, 'toggleStatus'])->name('faqs.toggle-status');

            Route::resource('team', TeamController::class)->except(['show']);

            // ----------------------------------------------------------
            // Phase 6 — Careers, Applications, Enquiries, Newsletter
            // ----------------------------------------------------------
            Route::resource('careers', CareerController::class)->except(['show']);

            Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
            Route::get('applications/{id}', [ApplicationController::class, 'show'])->name('applications.show');
            Route::patch('applications/{id}/status', [ApplicationController::class, 'updateStatus'])->name('applications.update-status');
            Route::get('applications/{id}/resume', [ApplicationController::class, 'downloadResume'])->name('applications.resume');
            Route::delete('applications/{id}', [ApplicationController::class, 'destroy'])->name('applications.destroy');

            Route::delete('enquiries/bulk-delete', [EnquiryController::class, 'bulkDestroy'])->name('enquiries.bulk-delete');
            Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
            Route::get('enquiries/{id}', [EnquiryController::class, 'show'])->name('enquiries.show');
            Route::patch('enquiries/{id}/status', [EnquiryController::class, 'updateStatus'])->name('enquiries.update-status');
            Route::delete('enquiries/{id}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');

            Route::get('subscribers/export', [SubscriberController::class, 'exportCsv'])->name('subscribers.export');
            Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
            Route::patch('subscribers/{id}/toggle-status', [SubscriberController::class, 'toggleStatus'])->name('subscribers.toggle-status');
            Route::delete('subscribers/{id}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');

            // ----------------------------------------------------------
            // Phase 7 — Media Manager, SEO Manager, Settings
            // ----------------------------------------------------------
            Route::get('media', [MediaController::class, 'index'])->name('media.index');
            Route::post('media', [MediaController::class, 'store'])->name('media.store');
            Route::put('media/{id}', [MediaController::class, 'update'])->name('media.update');
            Route::delete('media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');

            Route::get('seo', [SeoController::class, 'index'])->name('seo.index');
            Route::get('seo/{id}/edit', [SeoController::class, 'edit'])->name('seo.edit');
            Route::put('seo/{id}', [SeoController::class, 'update'])->name('seo.update');

            Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
            Route::put('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general.update');
            Route::put('settings/social', [SettingController::class, 'updateSocial'])->name('settings.social.update');
            Route::put('settings/contact', [SettingController::class, 'updateContact'])->name('settings.contact.update');
            Route::put('settings/smtp', [SettingController::class, 'updateSmtp'])->name('settings.smtp.update');

            // ----------------------------------------------------------
            // Phase 8 — Activity Logs, Security Hardening, Performance
            // ----------------------------------------------------------
            Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        });
    });
