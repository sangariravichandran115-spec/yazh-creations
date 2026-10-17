<nav class="navbar navbar-expand-lg yc-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand yc-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('images/branding/logo.png') }}" alt="{{ $brandName }} logo" class="yc-brand-logo me-2" width="40" height="40">
            <span class="yc-brand-text">
                {{ $brandName }}
                <small class="yc-brand-tagline d-block">{{ $tagline }}</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a>
                </li>
                @if ($navCategories->isNotEmpty())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('categories.show') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Categories</a>
                        <ul class="dropdown-menu yc-dropdown">
                            @foreach ($navCategories as $category)
                                <li>
                                    <a class="dropdown-item" href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact.index') ? 'active' : '' }}" href="{{ route('contact.index') }}">Contact</a>
                </li>
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <x-whatsapp-button class="btn-sm" message="Hello! I would like to know more about your collection." />
                </li>
            </ul>
        </div>
    </div>
</nav>