@extends('layouts.app')

@section('title', 'Session Expired — ' . $brandName)

@section('content')
    <section class="yc-section text-center error-page">
        <div class="container">
            <i class="bi bi-hourglass-split yc-error-icon"></i>
            <h1 class="yc-error-title">419</h1>
            <h2 class="mb-3">Your session has expired</h2>
            <p class="text-muted mb-4">Please go back and try again.</p>
            <a href="{{ url()->previous() ?? route('home') }}" class="btn btn-yc btn-lg"><i class="bi bi-arrow-repeat me-1"></i>Try Again</a>
        </div>
    </section>
@endsection