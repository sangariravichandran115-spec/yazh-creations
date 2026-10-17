<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $brandName . ' — Admin')</title>
    <meta name="robots" content="noindex, nofollow">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="yc-auth-body d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-6 col-lg-4">
                <div class="yc-auth-card rounded-4 p-4 p-md-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/branding/logo.png') }}" alt="{{ $brandName }} logo" class="mb-3" width="72" height="72">
                        <h1 class="yc-auth-title mb-1">{{ $brandName }}</h1>
                        <p class="yc-auth-subtitle mb-0">@yield('subtitle', 'Admin Login')</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success rounded-3 small">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 small">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>

                <p class="text-center text-muted small mt-3 mb-0">
                    <a href="{{ route('home') }}"><i class="bi bi-arrow-left me-1"></i>Back to website</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>