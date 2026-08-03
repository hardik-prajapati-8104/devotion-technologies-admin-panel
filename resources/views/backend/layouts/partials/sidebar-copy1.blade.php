<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <img src="{{ url('backend/images/devotion-technology.png') }}" alt="Devotion Technology" onerror="this.style.display='none'" width="100%"> 
    </div>

    <nav class="sidebar-nav">
        <ul class="list-unstyled">

            <li>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
                </a>
            </li>

            <div class="nav-section-title">Website Content</div> 

            @canany(['services.view'])
            <li class="has-submenu {{ request()->routeIs('admin.services*') ? 'open' : '' }}">
                <a href="#" class="nav-link" data-submenu-toggle>
                    <i class="bi bi-briefcase-fill"></i><span>Services</span>
                    <i class="bi bi-chevron-right submenu-arrow"></i>
                </a>
                <ul class="submenu list-unstyled">
                    <li><a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">All Services</a></li>
                    <li><a href="{{ route('admin.service-categories.index') }}" class="nav-link {{ request()->routeIs('admin.service-categories.*') ? 'active' : '' }}">Categories</a></li>
                </ul>
            </li>
            @endcanany

            @canany(['projects.view'])
            <li class="has-submenu {{ request()->routeIs('admin.projects*') ? 'open' : '' }}">
                <a href="#" class="nav-link" data-submenu-toggle>
                    <i class="bi bi-kanban-fill"></i><span>Projects</span>
                    <i class="bi bi-chevron-right submenu-arrow"></i>
                </a>
                <ul class="submenu list-unstyled">
                    <li><a href="{{ route('admin.projects.index') }}" class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">All Projects</a></li>
                    <li><a href="{{ route('admin.project-categories.index') }}" class="nav-link {{ request()->routeIs('admin.project-categories.*') ? 'active' : '' }}">Categories</a></li>
                </ul>
            </li>
            @endcanany

            @canany(['blogs.view'])
            <li class="has-submenu {{ request()->routeIs('admin.blogs*') || request()->routeIs('admin.blog-categories*') || request()->routeIs('admin.blog-tags*') ? 'open' : '' }}">
                <a href="#" class="nav-link" data-submenu-toggle>
                    <i class="bi bi-journal-richtext"></i><span>Blog Management</span>
                    <i class="bi bi-chevron-right submenu-arrow"></i>
                </a>
                <ul class="submenu list-unstyled">
                    <li><a href="{{ route('admin.blogs.index') }}" class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">All Blogs</a></li>
                    <li><a href="{{ route('admin.blog-categories.index') }}" class="nav-link {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}">Categories</a></li>
                    <li><a href="{{ route('admin.blog-tags.index') }}" class="nav-link {{ request()->routeIs('admin.blog-tags.*') ? 'active' : '' }}">Tags</a></li>
                </ul>
            </li>
            @endcanany

            @canany(['testimonials.view'])
            <li>
                <a href="{{ route('admin.testimonials.index') }}" class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-quote-fill"></i><span>Testimonials</span>
                </a>
            </li>
            @endcanany

            @canany(['faqs.view'])
            <li class="has-submenu {{ request()->routeIs('admin.faqs*') || request()->routeIs('admin.faq-categories*') ? 'open' : '' }}">
                <a href="#" class="nav-link" data-submenu-toggle>
                    <i class="bi bi-patch-question-fill"></i><span>FAQs</span>
                    <i class="bi bi-chevron-right submenu-arrow"></i>
                </a>
                <ul class="submenu list-unstyled">
                    <li><a href="{{ route('admin.faqs.index') }}" class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">All FAQs</a></li>
                    <li><a href="{{ route('admin.faq-categories.index') }}" class="nav-link {{ request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}">Categories</a></li>
                </ul>
            </li>
            @endcanany

            @canany(['team.view'])
            <li>
                <a href="{{ route('admin.team.index') }}" class="nav-link {{ request()->routeIs('admin.team.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i><span>Team Management</span>
                </a>
            </li>
            @endcanany

            <div class="nav-section-title">Recruitment</div>

            @canany(['careers.view'])
            <li>
                <a href="{{ route('admin.careers.index') }}" class="nav-link {{ request()->routeIs('admin.careers.*') ? 'active' : '' }}">
                    <i class="bi bi-briefcase-fill"></i><span>Careers</span>
                </a>
            </li>
            @endcanany

            @canany(['applications.view'])
            <li>
                <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-person-fill"></i><span>Applications</span>
                    @php $newApplications = \Illuminate\Support\Facades\Cache::remember('sidebar_new_applications', 60, fn () => \App\Models\CareerApplication::where('status', 'new')->count()); @endphp
                    @if ($newApplications > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $newApplications }}</span>
                    @endif
                </a>
            </li>
            @endcanany

            <div class="nav-section-title">Communication</div>

            @canany(['enquiries.view'])
            <li>
                <a href="{{ route('admin.enquiries.index') }}" class="nav-link {{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope-fill"></i><span>Contact Enquiries</span>
                    @php $newEnquiries = \Illuminate\Support\Facades\Cache::remember('sidebar_new_enquiries', 60, fn () => \App\Models\ContactEnquiry::where('status', 'new')->count()); @endphp
                    @if ($newEnquiries > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $newEnquiries }}</span>
                    @endif
                </a>
            </li>
            @endcanany

            @canany(['subscribers.view'])
            <li>
                <a href="{{ route('admin.subscribers.index') }}" class="nav-link {{ request()->routeIs('admin.subscribers.*') ? 'active' : '' }}">
                    <i class="bi bi-newspaper"></i><span>Newsletter Subscribers</span>
                </a>
            </li>
            @endcanany

            <div class="nav-section-title">System</div>

            @canany(['media.view'])
            <li>
                <a href="{{ route('admin.media.index') }}" class="nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                    <i class="bi bi-images"></i><span>Media / Gallery</span>
                </a>
            </li>
            @endcanany

            @canany(['seo.view'])
            <li>
                <a href="{{ route('admin.seo.index') }}" class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                    <i class="bi bi-search"></i><span>SEO Management</span>
                </a>
            </li>
            @endcanany

            @canany(['users.view'])
            <li>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i><span>Users</span>
                </a>
            </li>
            @endcanany

            @canany(['roles.view'])
            <li>
                <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock-fill"></i><span>Roles &amp; Permissions</span>
                </a>
            </li>
            @endcanany

            @canany(['settings.view'])
            <li>
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i><span>Settings</span>
                </a>
            </li>
            @endcanany

            @canany(['activity-logs.view'])
            <li>
                <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i><span>Activity Logs</span>
                </a>
            </li>
            @endcanany

            <li>
                <a href="{{ route('admin.profile.edit') }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i><span>Profile</span>
                </a>
            </li>

            <li>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i><span>Logout</span>
                </a>
            </li>

        </ul>
    </nav>
</aside>
