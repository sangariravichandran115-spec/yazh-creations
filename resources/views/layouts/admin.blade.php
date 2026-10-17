<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin — ' . $brandName)</title>
    <meta name="robots" content="noindex, nofollow">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="yc-admin-body">
    <div class="admin-shell d-flex">
        <aside class="admin-sidebar d-flex flex-column" id="adminSidebar">
            <a class="admin-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/branding/logo.png') }}" alt="" width="32" height="32" class="rounded">
                <span>{{ $brandName }}<small class="d-block admin-brand-sub">Admin</small></span>
            </a>

            <nav class="admin-nav flex-grow-1">
                <a class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                    <i class="bi bi-box-seam"></i> Products
                </a>
                <a class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags"></i> Categories
                </a>
                <a class="admin-nav-link {{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}" href="{{ route('admin.enquiries.index') }}">
                    <i class="bi bi-chat-dots"></i> Enquiries
                </a>
                <a class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <a href="{{ route('home') }}" class="admin-nav-link" target="_blank">
                    <i class="bi bi-globe"></i> View Website
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-nav-link admin-nav-link-btn w-100 border-0 bg-transparent text-start">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-main flex-grow-1 d-flex flex-column">
            <header class="admin-topbar d-flex align-items-center justify-content-between px-4">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#adminSidebar" aria-expanded="false" aria-controls="adminSidebar">
                    <i class="bi bi-list"></i>
                </button>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <span class="admin-user small">
                        <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                    </span>
                </div>
            </header>

            <main class="admin-content flex-grow-1 p-3 p-md-4">
                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')

                @stack('forms')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/custom.js') }}" defer></script>
    @stack('scripts')
</body>
</html>