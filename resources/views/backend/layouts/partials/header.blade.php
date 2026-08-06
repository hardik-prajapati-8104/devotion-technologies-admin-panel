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

        {{-- Theme & Cache Clear --}}
        <button class="header-icon-btn" type="button" title="Theme & Cache" data-bs-toggle="dropdown" data-bs-auto-close="outside">
            <i class="bi bi-palette-fill"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width:260px;">
            <div class="small text-uppercase text-muted fw-medium mb-2">Theme</div>
            <div class="btn-group w-100 mb-3" role="group" id="themeSwitcher">
                <button type="button" class="btn btn-sm btn-outline-secondary theme-option" data-theme="light">
                    <i class="bi bi-sun me-1"></i>Light
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary theme-option" data-theme="dark">
                    <i class="bi bi-moon-stars me-1"></i>Dark
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary theme-option" data-theme="system">
                    <i class="bi bi-circle-half me-1"></i>Auto
                </button>
            </div>

            @if ($admin?->hasRole('superadmin') || $admin?->can('settings.edit'))
                <hr class="my-2">
                <div class="small text-uppercase text-muted fw-medium mb-2">Cache</div>
                <button type="button" class="btn btn-sm btn-outline-danger w-100" id="clearCacheBtn">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    <span id="clearCacheBtnLabel">Clear Cache</span>
                </button>
                <div class="form-text mt-1 mb-0">Clears config, route, view &amp; app cache.</div>
            @endif
        </div>

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

<script>
(function () {
    const STORAGE_KEY = 'admin-theme'; // 'light' | 'dark' | 'system'

    function systemPrefersDark() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function applyTheme(pref) {
        const resolved = pref === 'system' ? (systemPrefersDark() ? 'dark' : 'light') : pref;
        document.documentElement.setAttribute('data-bs-theme', resolved);

        document.querySelectorAll('.theme-option').forEach(btn => {
            btn.classList.toggle('btn-secondary', btn.dataset.theme === pref);
            btn.classList.toggle('btn-outline-secondary', btn.dataset.theme !== pref);
        });
    }

    function currentPref() {
        return localStorage.getItem(STORAGE_KEY) || 'system';
    }

    // Apply on load (also mirrored as an early inline script in the
    // layout <head> — see setup notes — to avoid a flash of the wrong
    // theme before this file loads).
    applyTheme(currentPref());

    document.querySelectorAll('.theme-option').forEach(btn => {
        btn.addEventListener('click', function () {
            localStorage.setItem(STORAGE_KEY, this.dataset.theme);
            applyTheme(this.dataset.theme);
        });
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
        if (currentPref() === 'system') applyTheme('system');
    });

    // ---- Clear cache ----
    const clearBtn = document.getElementById('clearCacheBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            const label = document.getElementById('clearCacheBtnLabel');
            const originalLabel = label.textContent;

            clearBtn.disabled = true;
            label.textContent = 'Clearing...';

            fetch('{{ route('admin.system.clear-cache') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
                .then(res => res.json())
                .then(data => {
                    label.textContent = data.success ? 'Cleared!' : 'Some steps failed';
                    clearBtn.classList.toggle('btn-outline-danger', false);
                    clearBtn.classList.toggle('btn-outline-success', data.success);
                    clearBtn.classList.toggle('btn-outline-warning', !data.success);
                })
                .catch(() => {
                    label.textContent = 'Failed — try again';
                })
                .finally(() => {
                    setTimeout(() => {
                        clearBtn.disabled = false;
                        label.textContent = originalLabel;
                        clearBtn.classList.remove('btn-outline-success', 'btn-outline-warning');
                        clearBtn.classList.add('btn-outline-danger');
                    }, 2500);
                });
        });
    }
})();
</script>