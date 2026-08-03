@php $admin = Auth::guard('admin')->user(); @endphp

<header class="admin-header">
    <div class="d-flex align-items-center gap-3">
        <button class="header-toggle-btn" id="sidebarToggleBtn" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="header-search d-none d-md-block">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control form-control-sm" placeholder="Search...">
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">

        <button class="header-icon-btn" type="button" title="Notifications" data-bs-toggle="dropdown">
            <i class="bi bi-bell-fill"></i>
            <span class="badge rounded-pill bg-danger">3</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" style="min-width:280px;">
            <li class="dropdown-header">Notifications</li>
            <li><a class="dropdown-item small" href="#">New enquiry received.</a></li>
            <li><a class="dropdown-item small" href="#">New career application.</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item small text-center" href="#">View all</a></li>
        </ul>

        <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 header-profile" type="button" data-bs-toggle="dropdown">
                <img src="{{ $admin?->profile_image ? url('public/storage/'.$admin->profile_image) : 'https://ui-avatars.com/api/?background=aa8038&color=fff&name='.urlencode($admin->name ?? 'Admin') }}" alt="{{ $admin->name ?? 'Admin' }}">
                <span class="d-none d-md-inline fw-medium">{{ $admin->name ?? 'Admin' }}</span>
                <i class="bi bi-chevron-down small d-none d-md-inline"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.profile.change-password') }}"><i class="bi bi-shield-lock me-2"></i>Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
