@extends('layouts.app')

@section('title', 'Too Many Requests — ' . $brandName)

@section('content')
    <section class="yc-section text-center error-page">
        <div class="container">
            <i class="bi bi-stopwatch yc-error-icon"></i>
            <h1 class="yc-error-title">429</h1>
            <h2 class="mb-3">Too many requests</h2>
            <p class="text-muted mb-4">Please wait a moment and try again.</p>
            <a href="{{ route('home') }}" class="btn btn-yc btn-lg"><i class="bi bi-house me-1"></i>Back to Home</a>
        </div>
    </section>
@endsection