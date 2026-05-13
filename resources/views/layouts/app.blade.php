<!DOCTYPE html>
<html lang="en" data-bs-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HRIS Absensi') - {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.39.0/tabler-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="page">
        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('dashboard') }}">
                        <img src="https://tabler.io/static/logo.svg" width="110" height="32" alt="Tabler" class="navbar-brand-image">
                    </a>
                </h1>
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-0">
                        @php
                            $sidebarMenus = app('App\Services\MenuService')->buildSidebar(auth()->user()->roles->first());
                        @endphp
                        @foreach($sidebarMenus as $menuGroup)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-{{ Str::slug($menuGroup['name']) }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ collect($menuGroup['menus'])->filter(fn($m) => request()->routeIs(($m['route'] ?? '') . '*'))->isNotEmpty() ? 'true' : 'false' }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">@if(!empty($menuGroup['icon']))<i class="{{ $menuGroup['icon'] }}"></i>@endif</span>
                                    <span class="nav-link-title">{{ $menuGroup['name'] }}</span>
                                </a>
                                @if(count($menuGroup['menus'] ?? []) > 0)
                                    <div class="collapse{{ collect($menuGroup['menus'])->filter(fn($m) => request()->routeIs(($m['route'] ?? '') . '*'))->isNotEmpty() ? ' show' : '' }}" id="navbar-{{ Str::slug($menuGroup['name']) }}">
                                        <ul class="nav nav-sm flex-column">
                                            @foreach($menuGroup['menus'] as $menu)
                                                <li class="nav-item">
                                                    @if(($menu['route'] ?? '') === 'logout')
                                                        <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="{{ $menu['icon'] ?? 'ti ti-logout' }}"></i></span>
                                                            <span class="nav-link-title">{{ $menu['name'] }}</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ ($menu['route'] ?? false) ? route($menu['route']) : ($menu['url'] ?? '#') }}" class="nav-link{{ request()->routeIs(($menu['route'] ?? '') . '*') ? ' active' : '' }}">
                                                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="{{ $menu['icon'] ?? 'ti ti-circle' }}"></i></span>
                                                            <span class="nav-link-title">{{ $menu['name'] }}</span>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </aside>

        <div class="page-wrapper">
            <header class="navbar navbar-expand-md navbar-light sticky-top">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="navbar-nav flex-row order-md-last">
                        <div class="nav-item">
                            <a href="#" class="nav-link px-0" id="dark-mode-toggle">
                                <i class="ti ti-moon"></i>
                            </a>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link" data-bs-toggle="dropdown">
                                <i class="ti ti-bell"></i>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="badge bg-red">{{ auth()->user()->unreadNotifications->count() }}</span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-card">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Notifications</h3>
                                    </div>
                                    <div class="list-group list-group-flush list-group-hoverable">
                                        @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                            <a href="{{ route('notifications.index') }}" class="list-group-item">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <i class="ti ti-bell-ringing text-primary"></i>
                                                    </div>
                                                    <div class="col text-truncate">
                                                        <p class="text-body d-block">{{ $notification->data['message'] ?? 'Notification' }}</p>
                                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="list-group-item text-center text-muted">No notifications</div>
                                        @endforelse
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('notifications.index') }}" class="btn btn-primary btn-sm w-100">View All</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex align-items-center" data-bs-toggle="dropdown">
                                <span class="avatar avatar-sm me-2" style="background-image: url({{ auth()->user()->employee?->photo ? asset('storage/' . auth()->user()->employee->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }})"></span>
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="ti ti-user me-2"></i> Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ti ti-logout me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="page-body">
                <div class="container-xl">
                    @if(session('toast_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('toast_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('toast_error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('toast_error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>

            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center">
                        <div class="col-12 text-muted">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta19/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('dark-mode-toggle');
            const html = document.documentElement;
            if (toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentTheme = html.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    html.setAttribute('data-bs-theme', newTheme);
                    toggle.querySelector('i').className = newTheme === 'dark' ? 'ti ti-sun' : 'ti ti-moon';
                    fetch('/theme/toggle', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ theme: newTheme })
                    });
                });
            }
            document.querySelectorAll('.tom-select').forEach(function(el) {
                new TomSelect(el);
            });
            document.querySelectorAll('.datepicker').forEach(function(el) {
                flatpickr(el, { dateFormat: 'Y-m-d' });
            });
            document.querySelectorAll('.datetimepicker').forEach(function(el) {
                flatpickr(el, { enableTime: true, dateFormat: 'Y-m-d H:i:s' });
            });
        });

        function confirmDelete(message) {
            message = message || 'Are you sure?';
            return Swal.fire({
                title: 'Confirm',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
