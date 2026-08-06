<?php

use App\Http\Controllers\Backend\AdminMenuController;
use App\Http\Controllers\Backend\ActivityLogController;
use App\Http\Controllers\Backend\ApplicationController;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\BlogCategoryController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\BlogTagController;
use App\Http\Controllers\Backend\CareerController;
use App\Http\Controllers\Backend\CountryController;
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

 
use App\Http\Controllers\Backend\AnnouncementController;
use App\Http\Controllers\Backend\ChatController;
use App\Http\Controllers\Backend\ChatGroupController;
use App\Http\Controllers\Backend\ChatMessageController;
use App\Http\Controllers\Backend\MessageController;
use App\Http\Controllers\Backend\NoticeController;
use App\Http\Controllers\Backend\SupportTicketController;
use App\Http\Controllers\Backend\SystemController;
use Illuminate\Support\Facades\Broadcast;

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


            // Internal Chat (group channels)
            Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
            Route::post('chat', [ChatController::class, 'store'])->name('chat.store');
            Route::post('chat/{conversation}/send', [ChatController::class, 'send'])->name('chat.send');

            Route::get('chat-messages/{message}/info', [ChatMessageController::class, 'info'])->name('chat-messages.info');
            Route::post('chat-messages/{message}/react', [ChatMessageController::class, 'react'])->name('chat-messages.react');
            Route::post('chat-messages/{message}/pin', [ChatMessageController::class, 'togglePin'])->name('chat-messages.pin');
            Route::post('chat-messages/{message}/star', [ChatMessageController::class, 'toggleStar'])->name('chat-messages.star');
            Route::post('chat-messages/{message}/forward', [ChatMessageController::class, 'forward'])->name('chat-messages.forward');
            Route::delete('chat-messages/{message}', [ChatMessageController::class, 'destroy'])->name('chat-messages.destroy');
            
            Route::put('chat/groups/{id}', [ChatGroupController::class, 'update'])->name('chat.groups.update');
            Route::post('chat/groups/{id}/members', [ChatGroupController::class, 'addMembers'])->name('chat.groups.add-members');
            Route::delete('chat/groups/{id}/members/{adminId}', [ChatGroupController::class, 'removeMember'])->name('chat.groups.remove-member');
            Route::put('chat/groups/{id}/members/{adminId}/admin', [ChatGroupController::class, 'toggleAdmin'])->name('chat.groups.toggle-admin');
            Route::delete('chat/groups/{id}/clear', [ChatGroupController::class, 'clearChat'])->name('chat.groups.clear');
            Route::delete('chat/groups/{id}/exit', [ChatGroupController::class, 'exit'])->name('chat.groups.exit');
            Route::delete('chat/groups/{id}', [ChatGroupController::class, 'destroy'])->name('chat.groups.destroy');
            

            // Messages (1:1 direct)
            Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
            Route::post('messages/{conversation}/send', [MessageController::class, 'send'])->name('messages.send');

            // Announcements (org-wide)
            Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
            Route::get('announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
            Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
            Route::get('announcements/{id}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
            Route::put('announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
            Route::delete('announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

            // Notices (targeted/dept)
            Route::get('notices', [NoticeController::class, 'index'])->name('notices.index');
            Route::get('notices/create', [NoticeController::class, 'create'])->name('notices.create');
            Route::post('notices', [NoticeController::class, 'store'])->name('notices.store');
            Route::get('notices/{id}/edit', [NoticeController::class, 'edit'])->name('notices.edit');
            Route::put('notices/{id}', [NoticeController::class, 'update'])->name('notices.update');
            Route::delete('notices/{id}', [NoticeController::class, 'destroy'])->name('notices.destroy');

            // Support Tickets
            Route::get('support-tickets', [SupportTicketController::class, 'index'])->name('support-tickets.index');
            Route::get('support-tickets/create', [SupportTicketController::class, 'create'])->name('support-tickets.create');
            Route::post('support-tickets', [SupportTicketController::class, 'store'])->name('support-tickets.store');
            Route::get('support-tickets/{id}', [SupportTicketController::class, 'show'])->name('support-tickets.show');
            Route::post('support-tickets/{id}/reply', [SupportTicketController::class, 'reply'])->name('support-tickets.reply');
            Route::put('support-tickets/{id}/status', [SupportTicketController::class, 'updateStatus'])->name('support-tickets.status');
            Route::put('support-tickets/{id}/assign', [SupportTicketController::class, 'assign'])->name('support-tickets.assign');
            Route::delete('support-tickets/{id}', [SupportTicketController::class, 'destroy'])->name('support-tickets.destroy');


            // ----------------------------------------------------------
            // Phase 7 — Media Manager, SEO Manager, Settings
            // ----------------------------------------------------------
            Route::get('media', [MediaController::class, 'index'])->name('media.index');
            Route::post('media', [MediaController::class, 'store'])->name('media.store');
            Route::get('media/{id}', [MediaController::class, 'show'])->name('media.show');
            Route::put('media/{id}', [MediaController::class, 'update'])->name('media.update');
            Route::delete('media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');

            Route::get('countries', [CountryController::class, 'index'])->name('countries.index');
            Route::get('countries/create', [CountryController::class, 'create'])->name('countries.create');
            Route::post('countries', [CountryController::class, 'store'])->name('countries.store');
            Route::get('countries/{id}/edit', [CountryController::class, 'edit'])->name('countries.edit');
            Route::put('countries/{id}', [CountryController::class, 'update'])->name('countries.update');
            Route::delete('countries/{id}', [CountryController::class, 'destroy'])->name('countries.destroy');
            Route::put('countries/{id}/toggle-status', [CountryController::class, 'toggleStatus'])->name('countries.toggle-status');

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
            Route::post('system/clear-cache', [SystemController::class, 'clearCache'])->name('system.clear-cache');
 
           
        });
    });

    Broadcast::routes(['middleware' => ['auth:admin']]);

