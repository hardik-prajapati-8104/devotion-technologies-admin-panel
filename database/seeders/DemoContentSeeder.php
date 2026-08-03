<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Career;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Realistic sample content so a fresh install of the admin panel
 * doesn't look empty. No image files are seeded (there aren't any to
 * point at yet) — every module renders fine without one, falling back
 * to placeholder avatars/icons in the UI.
 *
 * Safe to re-run: every insert is wrapped in firstOrCreate keyed on a
 * unique field, so running this twice won't create duplicates.
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $author = Admin::first();

        // ---------------------------------------------------------------
        // Services
        // ---------------------------------------------------------------
        $webCategory = ServiceCategory::firstOrCreate(
            ['slug' => 'web-development'],
            ['name' => 'Web Development', 'status' => 1]
        );
        $cloudCategory = ServiceCategory::firstOrCreate(
            ['slug' => 'cloud-and-devops'],
            ['name' => 'Cloud & DevOps', 'status' => 1]
        );

        $services = [
            ['name' => 'Custom Web Application Development', 'category' => $webCategory, 'icon' => 'bi-code-slash', 'short' => 'Scalable Laravel and React applications built around your workflow, not a template.'],
            ['name' => 'E-Commerce Development', 'category' => $webCategory, 'icon' => 'bi-cart3', 'short' => 'End-to-end online stores with secure payments, inventory, and order management.'],
            ['name' => 'Cloud Migration & Infrastructure', 'category' => $cloudCategory, 'icon' => 'bi-cloud-arrow-up', 'short' => 'Move confidently to AWS, GCP, or Azure with zero-downtime migration plans.'],
            ['name' => 'DevOps & CI/CD Automation', 'category' => $cloudCategory, 'icon' => 'bi-gear-wide-connected', 'short' => 'Automated pipelines that ship code safely, multiple times a day.'],
        ];

        foreach ($services as $i => $s) {
            Service::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($s['name'])],
                [
                    'service_category_id' => $s['category']->id,
                    'name' => $s['name'],
                    'short_description' => $s['short'],
                    'full_description' => $s['short'].' Our team works closely with your stakeholders from discovery through launch and beyond.',
                    'icon' => $s['icon'],
                    'display_order' => $i + 1,
                    'is_featured' => $i < 2,
                    'status' => 1,
                ]
            );
        }

        // ---------------------------------------------------------------
        // Projects
        // ---------------------------------------------------------------
        $fintechCategory = ProjectCategory::firstOrCreate(['slug' => 'fintech'], ['name' => 'Fintech', 'status' => 1]);
        $healthCategory = ProjectCategory::firstOrCreate(['slug' => 'healthcare'], ['name' => 'Healthcare', 'status' => 1]);

        $projects = [
            ['name' => 'PayStream Merchant Dashboard', 'category' => $fintechCategory, 'client' => 'PayStream Inc.', 'status' => 'completed', 'tech' => 'Laravel, Vue, MySQL, Stripe'],
            ['name' => 'MediTrack Patient Portal', 'category' => $healthCategory, 'client' => 'MediTrack Health', 'status' => 'in_progress', 'tech' => 'Laravel, React, PostgreSQL'],
            ['name' => 'LedgerSync Accounting API', 'category' => $fintechCategory, 'client' => 'LedgerSync', 'status' => 'completed', 'tech' => 'Laravel, MySQL, Redis'],
        ];

        foreach ($projects as $i => $p) {
            Project::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($p['name'])],
                [
                    'project_category_id' => $p['category']->id,
                    'name' => $p['name'],
                    'client_name' => $p['client'],
                    'short_description' => 'A '.strtolower($p['category']->name).' platform built to handle real-world scale from day one.',
                    'technologies' => $p['tech'],
                    'status' => $p['status'],
                    'is_featured' => $i === 0,
                    'display_order' => $i + 1,
                    'completion_date' => $p['status'] === 'completed' ? now()->subMonths($i + 1) : null,
                ]
            );
        }

        // ---------------------------------------------------------------
        // Blogs
        // ---------------------------------------------------------------
        $newsCategory = BlogCategory::firstOrCreate(['slug' => 'company-news'], ['name' => 'Company News', 'status' => 1]);
        $techCategory = BlogCategory::firstOrCreate(['slug' => 'engineering'], ['name' => 'Engineering', 'status' => 1]);

        $blogs = [
            ['title' => 'Devotion Technology Opens New Development Hub', 'category' => $newsCategory, 'status' => 'published'],
            ['title' => 'Five Laravel Performance Tips We Use on Every Project', 'category' => $techCategory, 'status' => 'published'],
            ['title' => 'Why We Chose Vue Over React for Our Latest Client', 'category' => $techCategory, 'status' => 'draft'],
        ];

        foreach ($blogs as $b) {
            Blog::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($b['title'])],
                [
                    'blog_category_id' => $b['category']->id,
                    'admin_id' => $author?->id,
                    'title' => $b['title'],
                    'short_description' => 'A quick look at what this means for our clients and our team.',
                    'content' => '<p>This is placeholder content for demo purposes. Replace with the real article body.</p>',
                    'status' => $b['status'],
                    'publish_date' => $b['status'] === 'published' ? now()->subWeeks(rand(1, 8)) : null,
                    'reading_time' => rand(3, 7),
                ]
            );
        }

        // ---------------------------------------------------------------
        // Testimonials
        // ---------------------------------------------------------------
        $testimonials = [
            ['name' => 'Ananya Sharma', 'company' => 'PayStream Inc.', 'designation' => 'VP of Engineering', 'rating' => 5, 'text' => 'Devotion Technology delivered ahead of schedule and the code quality has made our own team faster ever since.'],
            ['name' => 'Marcus Webb', 'company' => 'MediTrack Health', 'designation' => 'Product Manager', 'rating' => 5, 'text' => 'Clear communication throughout and a genuinely collaborative process. Exactly what we needed for a healthcare platform.'],
            ['name' => 'Priya Nair', 'company' => 'LedgerSync', 'designation' => 'Founder & CEO', 'rating' => 4, 'text' => 'Solid technical execution and thoughtful suggestions that improved our original spec.'],
        ];

        foreach ($testimonials as $i => $t) {
            Testimonial::firstOrCreate(
                ['client_name' => $t['name'], 'company' => $t['company']],
                [
                    'designation' => $t['designation'],
                    'rating' => $t['rating'],
                    'testimonial' => $t['text'],
                    'display_order' => $i + 1,
                    'is_featured' => $i === 0,
                    'status' => 1,
                ]
            );
        }

        // ---------------------------------------------------------------
        // FAQs
        // ---------------------------------------------------------------
        $generalFaqCategory = FaqCategory::firstOrCreate(['slug' => 'general'], ['name' => 'General', 'status' => 1, 'display_order' => 1]);
        $pricingFaqCategory = FaqCategory::firstOrCreate(['slug' => 'pricing'], ['name' => 'Pricing', 'status' => 1, 'display_order' => 2]);

        $faqs = [
            ['q' => 'What industries do you work with?', 'a' => 'We work primarily with fintech, healthcare, and e-commerce clients, but our approach applies to any custom software project.', 'cat' => $generalFaqCategory],
            ['q' => 'How long does a typical project take?', 'a' => 'Most engagements run 3-6 months from discovery to launch, depending on scope. We\'ll give you a realistic timeline before any contract is signed.', 'cat' => $generalFaqCategory],
            ['q' => 'How do you price your projects?', 'a' => 'We offer both fixed-price and time-and-materials engagements depending on how well-defined the requirements are up front.', 'cat' => $pricingFaqCategory],
        ];

        foreach ($faqs as $i => $f) {
            Faq::firstOrCreate(
                ['question' => $f['q']],
                ['answer' => $f['a'], 'faq_category_id' => $f['cat']->id, 'display_order' => $i + 1, 'status' => 1]
            );
        }

        // ---------------------------------------------------------------
        // Team
        // ---------------------------------------------------------------
        $team = [
            ['name' => 'Rohan Mehta', 'designation' => 'Founder & CEO', 'department' => 'Leadership'],
            ['name' => 'Sara Kim', 'designation' => 'Head of Engineering', 'department' => 'Engineering'],
            ['name' => 'Daniel Osei', 'designation' => 'Lead Product Designer', 'department' => 'Design'],
        ];

        foreach ($team as $i => $member) {
            TeamMember::firstOrCreate(
                ['name' => $member['name']],
                [
                    'designation' => $member['designation'],
                    'department' => $member['department'],
                    'biography' => 'A key member of the Devotion Technology team, focused on delivering great outcomes for every client.',
                    'display_order' => $i + 1,
                    'status' => 1,
                ]
            );
        }

        // ---------------------------------------------------------------
        // Careers
        // ---------------------------------------------------------------
        $careers = [
            ['title' => 'Senior Laravel Developer', 'type' => 'full_time', 'location' => 'Remote'],
            ['title' => 'Frontend Engineer (Vue/React)', 'type' => 'full_time', 'location' => 'Surat, Gujarat'],
            ['title' => 'DevOps Engineering Intern', 'type' => 'internship', 'location' => 'Remote'],
        ];

        foreach ($careers as $i => $c) {
            Career::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($c['title'])],
                [
                    'title' => $c['title'],
                    'employment_type' => $c['type'],
                    'location' => $c['location'],
                    'department' => 'Engineering',
                    'experience' => $c['type'] === 'internship' ? '0-1 years' : '3-5 years',
                    'short_description' => 'Join our growing engineering team and work on real client projects from day one.',
                    'description' => 'We\'re looking for a motivated engineer to join our team and help us deliver great software for our clients.',
                    'status' => 1,
                    'is_featured' => $i === 0,
                    'application_deadline' => now()->addMonths(2),
                ]
            );
        }
    }
}
