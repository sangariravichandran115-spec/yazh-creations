@extends('layouts.app')

@section('title', 'Server Error — ' . $brandName)

@section('content')
    <section class="yc-section text-center error-page">
        <div class="container">
            <i class="bi bi-tools yc-error-icon"></i>
            <h1 class="yc-error-title">500</h1>
            <h2 class="mb-3">Something went wrong</h2>
            <p class="text-muted mb-4">An unexpected error occurred. Please try again in a moment.</p>
            <a href="{{ route('home') }}" class="btn btn-yc btn-lg"><i class="bi bi-house me-1"></i>Back to Home</a>
        </div>
    </section>
@endsection