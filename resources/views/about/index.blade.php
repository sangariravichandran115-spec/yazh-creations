@extends('layouts.app')

@section('title', $storyTitle . ' — ' . $brandName)
@section('meta_description', 'Discover the story behind ' . $brandName . ' — women\'s fashion and handcrafted creations crafted with love.')

@section('content')
    <section class="yc-page-hero">
        <div class="container py-5 text-center">
            <h1 class="yc-page-title mb-2">{{ $storyTitle }}</h1>
            <p class="yc-page-subtitle mb-0">{{ $tagline }}</p>
        </div>
    </section>

    <section class="yc-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    @if ($storyImage)
                        <img src="{{ asset('storage/'.$storyImage) }}" alt="{{ $brandName }} story image" class="rounded-4 shadow mb-4 mx-auto d-block w-100" style="max-width: 400px;">
                    @else
                        <img src="{{ asset('images/branding/logo.png') }}" alt="{{ $brandName }} logo" class="rounded-4 shadow mb-4 mx-auto d-block" width="120" height="120">
                    @endif
                    <h2 class="yc-section-title text-center">{{ $brandName }}</h2>
                    <p class="text-center yc-section-subtitle">{{ $tagline }}</p>
                    <div class="yc-about-body">
                        <p>{{ $aboutText ?: 'Yazh Creations is a women\'s fashion and handcrafted accessories brand celebrating the art of handmade elegance.' }}</p>
                        <p>From trending kurtis, leggings and palazzos to intricate Aari work and handmade accessories, every piece is crafted with love, tradition and premium quality. We believe fashion should feel personal — which is why every enquiry on WhatsApp is answered with care.</p>
                        <blockquote class="blockquote text-center my-5 yc-blockquote">
                            <p>"Wear • Create • Inspire"</p>
                        </blockquote>
                        <p>Follow our journey on Instagram for daily style inspiration and be the first to see new arrivals from the atelier.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="yc-cta">
        <div class="container text-center py-5">
            <h2 class="yc-cta-title mb-3">Let's stay connected</h2>
            <p class="yc-cta-text mb-4">Ask us anything — we love hearing from you.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('contact.index') }}" class="btn btn-outline-light btn-lg">Contact Us</a>
                <x-whatsapp-button message="Hello! I found you on your About page." class="btn-lg" />
            </div>
        </div>
    </section>
@endsection