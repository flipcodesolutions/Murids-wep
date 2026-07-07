<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Murids Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <div class="logo-icon">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Murids" class="logo-img">
                    </div>

                </div>
                <button class="sidebar-close" id="sidebarClose" aria-label="Close menu">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-label">Main Menu</div>

                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <a href="{{ route('religions.index') }}" class="nav-link {{ request()->routeIs('religions.*') ? 'active' : '' }}">
                    <i class="bi bi-ui-checks"></i> Religions
                </a>

                <a href="{{ route('onboarding-steps.index') }}" class="nav-link {{ request()->routeIs('onboarding-steps.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-steps"></i> Onboarding Questions
                </a>

                <a href="{{ route('questions.index') }}" class="nav-link {{ request()->routeIs('questions.*') ? 'active' : '' }}">
                    <i class="bi bi-patch-question"></i> Questions
                </a>

                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Users
                </a>

                <a href="{{ route('answers.index') }}" class="nav-link {{ request()->routeIs('answers.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-square-text"></i> Reports
                </a>

                <div class="nav-label mt-3">Account</div>

                <a href="{{ route('logout') }}" class="nav-link">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">AD</div>
                    <div>
                        <div class="user-name">Admin User</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <div class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title">Murids Admin</h1>
                </div>
                <div class="header-right">
                    <button class="header-icon-btn" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                        <span class="badge-dot"></span>
                    </button>
                </div>
            </header>
            <!-- Main Content -->
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3000,
            extendedTimeOut: 1000,
            preventDuplicates: true,
        };
    </script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>

</html>
