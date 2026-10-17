@extends('layouts.app')

@section('title', 'Page Not Found — ' . $brandName)

@section('content')
    <section class="yc-section text-center error-page">
        <div class="container">
            <i class="bi bi-compass yc-error-icon"></i>
            <h1 class="yc-error-title">404</h1>
            <h2 class="mb-3">Oops, we couldn't find that page</h2>
            <p class="text-muted mb-4">The page you are looking for doesn't exist or has been moved.</p>
            <a href="{{ route('home') }}" class="btn btn-yc btn-lg"><i class="bi bi-house me-1"></i>Back to Home</a>
        </div>
    </section>
@endsection