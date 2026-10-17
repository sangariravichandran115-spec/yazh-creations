@extends('layouts.app')

@section('title', 'Contact — ' . $brandName)
@section('meta_description', 'Get in touch with ' . $brandName . ' for enquiries about kurtis, leggings, palazzos, Aari work and handmade accessories.')

@section('content')
    <section class="yc-page-hero">
        <div class="container py-5 text-center">
            <h1 class="yc-page-title mb-2">Contact Us</h1>
            <p class="yc-page-subtitle mb-0">We'd love to hear from you</p>
        </div>
    </section>

    <section class="yc-section">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="yc-contact-card rounded-4 p-4 text-center h-100">
                        <i class="bi bi-whatsapp yc-contact-icon"></i>
                        <h3 class="yc-contact-title">WhatsApp</h3>
                        <p class="mb-3">Fastest way to reach us for product enquiries.</p>
                        <x-whatsapp-button message="Hello! I would like to know more about your collection." />
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="yc-contact-card rounded-4 p-4 text-center h-100">
                        <i class="bi bi-envelope-fill yc-contact-icon"></i>
                        <h3 class="yc-contact-title">Email</h3>
                        @if ($email)
                            <p class="mb-3"><a href="mailto:{{ $email }}" class="yc-contact-link">{{ $email }}</a></p>
                        @else
                            <p class="mb-3">Send us a message anytime.</p>
                        @endif
                        <a href="mailto:{{ $email }}" class="btn btn-outline-yc">Send Email</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="yc-contact-card rounded-4 p-4 text-center h-100">
                        <i class="bi bi-chat-heart-fill yc-contact-icon"></i>
                        <h3 class="yc-contact-title">Instagram</h3>
                        <p class="mb-3">Follow us for daily style inspiration.</p>
                        <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-yc">Follow on Instagram</a>
                    </div>
                </div>

                @if ($address || $phone)
                    <div class="col-12">
                        <div class="yc-contact-info rounded-4 p-4 text-center">
                            @if ($address)
                                <p class="mb-2"><i class="bi bi-geo-alt-fill me-2 text-gold"></i>{{ $address }}</p>
                            @endif
                            @if ($phone)
                                <p class="mb-0"><i class="bi bi-telephone-fill me-2 text-gold"></i><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection