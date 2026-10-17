<footer class="yc-footer mt-auto">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <a class="d-flex align-items-center mb-3 text-decoration-none" href="{{ route('home') }}">
                    <img src="{{ asset('images/branding/logo.png') }}" alt="{{ $brandName }} logo" class="me-2 yc-footer-logo" width="40" height="40">
                    <span class="yc-footer-brand">{{ $brandName }}</span>
                </a>
                <p class="yc-footer-text">{{ $tagline }}</p>
                <p class="yc-footer-text small">{{ Str::limit($aboutText, 160) }}</p>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="yc-footer-heading">Explore</h6>
                <ul class="list-unstyled yc-footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('contact.index') }}">Contact</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="yc-footer-heading">Categories</h6>
                <ul class="list-unstyled yc-footer-links">
                    @foreach ($navCategories->take(6) as $category)
                        <li><a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="yc-footer-heading">Get In Touch</h6>
                <ul class="list-unstyled yc-footer-contact">
                    @if ($contactAddress)
                        <li><i class="bi bi-geo-alt-fill me-2"></i>{{ $contactAddress }}</li>
                    @endif
                    @if ($contactEmail)
                        <li><i class="bi bi-envelope-fill me-2"></i><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></li>
                    @endif
                    @if ($contactPhone)
                        <li><i class="bi bi-telephone-fill me-2"></i><a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}">{{ $contactPhone }}</a></li>
                    @endif
                    @if ($instagramUrl && $instagramUrl !== '#')
                        <li><i class="bi bi-instagram me-2"></i><a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer">Follow on Instagram</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <hr class="yc-footer-divider">

        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <p class="yc-footer-copy mb-0">&copy; {{ date('Y') }} {{ $brandName }}. All rights reserved.</p>
            <p class="yc-footer-copy mb-0">Wear &bull; Create &bull; Inspire</p>
        </div>
    </div>
</footer>