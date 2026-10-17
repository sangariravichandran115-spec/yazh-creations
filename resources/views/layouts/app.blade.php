<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $brandName . ' — ' . $tagline)</title>
    <meta name="description" content="@yield('meta_description', $tagline . '. Elegant women\'s fashion and handcrafted creations.')">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:title" content="@yield('og_title', $brandName . ' — ' . $tagline)">
    <meta property="og:description" content="@yield('og_description', $tagline . '. Elegant women\'s fashion and handcrafted creations.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/branding/logo.png'))">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @stack('meta')
</head>
<body class="d-flex flex-column min-vh-100 yc-body">
    @include('layouts.navbar')

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @include('layouts.footer')

    <x-whatsapp-button
        floating
        message="Hello! I would like to know more about your collection."
    />

    <script src="{{ asset('js/custom.js') }}" defer></script>

    @stack('scripts')
</body>
</html>