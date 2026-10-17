@extends('layouts.app')

@section('title', $brandName . ' — ' . $tagline)

@php
    $heroImage = $newArrivals->first()?->imageUrl() ?: asset('images/branding/logo.png');
@endphp

@section('content')
    <section class="yc-hero">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 text-center text-lg-start">
                    <img src="{{ asset('images/branding/logo.png') }}" alt="{{ $brandName }} logo" class="yc-hero-logo mb-4" width="88" height="88">
                    <h1 class="yc-hero-brand">{{ $brandName }}</h1>
                    <p class="yc-hero-tagline">{{ $tagline }}</p>
                    <p class="yc-hero-desc mt-3 mb-4">{{ $heroDescription }}</p>
                    <a href="{{ route('products.index') }}" class="btn btn-yc btn-lg">Explore Collection</a>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="{{ $heroImage }}" alt="Featured {{ $brandName }} product" class="yc-hero-image rounded-4 shadow">
                </div>
            </div>
        </div>
    </section>

    {{-- Featured categories --}}
    @if ($featuredCategories->isNotEmpty())
        <section class="yc-section">
            <div class="container">
                <div class="yc-section-head text-center mb-4">
                    <h2 class="yc-section-title">Shop by Category</h2>
                    <p class="yc-section-subtitle">Curated for every occasion</p>
                </div>
                <div class="row g-4">
                    @foreach ($featuredCategories as $category)
                        <div class="col-6 col-md-4 col-lg-3">
                            <x-category-card :category="$category" :product-count="$category->products_count" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- New arrivals --}}
    @if ($newArrivals->isNotEmpty())
        <section class="yc-section yc-section-cream">
            <div class="container">
                <div class="yc-section-head text-center mb-4">
                    <h2 class="yc-section-title">New Arrivals</h2>
                    <p class="yc-section-subtitle">Fresh from the atelier</p>
                </div>
                <div class="row g-4">
                    @foreach ($newArrivals as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-yc">View All Products</a>
                </div>
            </div>
        </section>
    @endif

    {{-- Featured products --}}
    @if ($featuredProducts->isNotEmpty())
        <section class="yc-section">
            <div class="container">
                <div class="yc-section-head text-center mb-4">
                    <h2 class="yc-section-title">Featured Products</h2>
                    <p class="yc-section-subtitle">Customer favourites</p>
                </div>
                <div class="row g-4">
                    @foreach ($featuredProducts as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Why choose us --}}
    <section class="yc-section yc-section-cream">
        <div class="container">
            <div class="yc-section-head text-center mb-4">
                <h2 class="yc-section-title">Why Choose {{ $brandName }}</h2>
                <p class="yc-section-subtitle">Made with kindness, worn with pride</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="yc-feature-card text-center rounded-4 p-4 h-100">
                        <i class="bi bi-heart-fill yc-feature-icon"></i>
                        <h3 class="yc-feature-title">Handcrafted with Love</h3>
                        <p class="yc-feature-text mb-0">Every piece is created with care and attention to detail.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="yc-feature-card text-center rounded-4 p-4 h-100">
                        <i class="bi bi-gem yc-feature-icon"></i>
                        <h3 class="yc-feature-title">Premium Fabrics</h3>
                        <p class="yc-feature-text mb-0">Thoughtfully chosen fabrics for comfort and elegance.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="yc-feature-card text-center rounded-4 p-4 h-100">
                        <i class="bi bi-flower1 yc-feature-icon"></i>
                        <h3 class="yc-feature-title">Aari Work Heritage</h3>
                        <p class="yc-feature-text mb-0">Intricate hand embroidery rooted in tradition.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="yc-feature-card text-center rounded-4 p-4 h-100">
                        <i class="bi bi-chat-dots-fill yc-feature-icon"></i>
                        <h3 class="yc-feature-title">Easy WhatsApp Enquiry</h3>
                        <p class="yc-feature-text mb-0">Message us directly for personalised shopping.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- About the brand --}}
    <section class="yc-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <img src="{{ $storyImage ? asset('storage/'.$storyImage) : asset('images/branding/logo.png') }}" alt="About {{ $brandName }}" class="rounded-4 shadow w-100">
                </div>
                <div class="col-lg-6">
                    <h2 class="yc-section-title">{{ $storyTitle }}</h2>
                    <p class="yc-section-subtitle">{{ $tagline }}</p>
                    <p class="yc-about-text">{{ $aboutText ?: 'Yazh Creations celebrates the art of handmade elegance — from trending kurtis, leggings and palazzos to intricate Aari work and handmade accessories.' }}</p>
                    <a href="{{ route('about') }}" class="btn btn-outline-yc">Learn More About Us</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Instagram --}}
    <section class="yc-section yc-section-cream">
        <div class="container text-center">
            <i class="bi bi-instagram yc-instagram-icon"></i>
            <h2 class="yc-section-title">Follow Us on Instagram</h2>
            <p class="yc-section-subtitle">Get daily style inspiration and see new arrivals first</p>
            <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-yc mt-2">{{ $brandName }} on Instagram</a>
        </div>
    </section>

    {{-- WhatsApp CTA --}}
    <section class="yc-cta">
        <div class="container text-center py-5">
            <h2 class="yc-cta-title mb-3">Ready to find your perfect piece?</h2>
            <p class="yc-cta-text mb-4">Ask about any product directly on WhatsApp — fast, friendly and personal.</p>
            <x-whatsapp-button message="Hello! I explored your website and would love to know more." class="btn-lg" />
        </div>
    </section>
@endsection