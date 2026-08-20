<aside class="admin-sidebar" id="adminSidebar">
    {{-- <div class="sidebar-brand">
        <img class="brand-logo-full" src="{{ url('public/backend/images/devotion-technology.png') }}" alt="Devotion Technology" onerror="this.style.display='none'" width="100%">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">  
            <img src="{{ url('public/frontend/images/Work_home_sefty_solution-footer.png') }}" alt="Company Logo" class="me-2" width="50px;" height="50px;">
            <span class="fs-6 text-white" style="font-style: poppins, sans-serif; text-align:start;">
                <b>WORK HOME</b>
                <br>
                <b>SAFETY SOLUTION</b>
            </span>
        </a>
        <img class="brand-logo-icon" src="{{ url('public/backend/images/favicon.png') }}" alt="Devotion Technology" onerror="this.style.display='none'">
    </div> --}}

    <div class="sidebar-brand">
        <img class="brand-logo-full"
            src="{{ isset($configurations['site_logo']) ? asset('storage/app/public/' . $configurations['site_logo']) : url('public/backend/images/devotion-technology.png') }}"
            alt="{{ config('app.name') }}"
            onerror="this.onerror=null;this.src='{{ url('public/backend/images/devotion-technology.png') }}';"
            width="100%">

        <img class="brand-logo-icon"
            src="{{ isset($configurations['site_favicon']) ? asset('storage/app/public/' . $configurations['site_favicon']) : url('public/backend/images/favicon.png') }}"
            alt="{{ config('app.name') }}"
            onerror="this.onerror=null;this.src='{{ url('public/backend/images/favicon.png') }}';">
    </div>

    <nav class="sidebar-nav">
        <ul class="list-unstyled">

            @php $menuTree = \App\Models\AdminMenu::tree(); @endphp

            @foreach ($menuTree as $menu)

                @if ($menu->type === 'section' && $menu->children->isNotEmpty())

                    <li class="has-submenu {{ $menu->is_active_route ? 'open' : '' }}">
                        <a href="#" class="nav-link" data-submenu-toggle>
                            @if ($menu->icon)<i class="{{ $menu->icon }}"></i>@endif
                            <span>{{ $menu->title }}</span>
                            <i class="bi bi-chevron-right submenu-arrow"></i>
                        </a>
                        <ul class="submenu list-unstyled">
                            @foreach ($menu->children as $child)
                                <li>
                                    <a href="{{ $child->url }}"
                                       class="nav-link {{ $child->is_active_route ? 'active' : '' }}"
                                       @if ($child->open_in_new_tab) target="_blank" @endif>
                                        @if ($child->icon)<i class="{{ $child->icon }}"></i>@endif
                                        <span>{{ $child->title }}</span>
                                        @if ($child->badge_count)
                                            <span class="badge bg-danger rounded-pill ms-auto">{{ $child->badge_count }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                @elseif ($menu->type === 'section')

                    <li class="nav-section-item">
                        <div class="nav-section-title">{{ $menu->title }}</div>
                    </li>

                @elseif ($menu->children->isNotEmpty())

                    {{-- Parent item with a submenu --}}
                    <li class="has-submenu {{ $menu->is_active_route ? 'open' : '' }}">
                        <a href="#" class="nav-link" data-submenu-toggle>
                            @if ($menu->icon)<i class="{{ $menu->icon }}"></i>@endif
                            <span>{{ $menu->title }}</span>
                            <i class="bi bi-chevron-right submenu-arrow"></i>
                        </a>
                        <ul class="submenu list-unstyled">
                            @foreach ($menu->children as $child)
                                <li>
                                    <a href="{{ $child->url }}"
                                       class="nav-link {{ $child->is_active_route ? 'active' : '' }}"
                                       @if ($child->open_in_new_tab) target="_blank" @endif>
                                        @if ($child->icon)<i class="{{ $child->icon }}"></i>@endif
                                        <span>{{ $child->title }}</span>
                                        @if ($child->badge_count)
                                            <span class="badge bg-danger rounded-pill ms-auto">{{ $child->badge_count }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                @else

                    {{-- Plain link item --}}
                    <li>
                        <a href="{{ $menu->url }}"
                           class="nav-link {{ $menu->is_active_route ? 'active' : '' }}"
                           @if ($menu->open_in_new_tab) target="_blank" @endif>
                            @if ($menu->icon)<i class="{{ $menu->icon }}"></i>@endif
                            <span>{{ $menu->title }}</span>
                            @if ($menu->badge_count)
                                <span class="badge bg-danger rounded-pill ms-auto">{{ $menu->badge_count }}</span>
                            @endif
                        </a>
                    </li>

                @endif

            @endforeach

            {{-- Logout stays hardcoded: it needs a POST form + CSRF token, not a plain link --}}
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
