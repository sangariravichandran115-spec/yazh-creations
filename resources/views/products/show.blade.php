@extends('layouts.app')

@section('title', $product->name . ' — ' . $brandName)
@section('meta_description', Str::limit($product->description, 160))
@section('og_title', $product->name . ' — ' . $brandName)
@section('og_description', Str::limit($product->description, 160))
@section('og_image', $product->imageUrl())

@section('content')
    <section class="yc-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb yc-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->category) }}">{{ $product->category->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>

            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="product-gallery">
                        @if ($product->images->isNotEmpty())
                            <img src="{{ $product->primaryImage()->url() }}" alt="{{ $product->name }} — main image" id="gallery-main" class="product-gallery-main rounded-4 w-100">
                            @if ($product->images->count() > 1)
                                <div class="product-gallery-thumbs d-flex gap-2 mt-3">
                                    @foreach ($product->images->sortBy('position') as $image)
                                        <button type="button" class="product-gallery-thumb rounded-3 p-0 border-0 {{ $image->is_primary ? 'active' : '' }}" data-gallery-src="{{ $image->url() }}" data-gallery-alt="{{ $product->name }}" aria-label="View product image">
                                            <img src="{{ $image->url() }}" alt="{{ $product->name }}" class="rounded-3">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <img src="{{ asset('images/placeholder-product.jpg') }}" alt="{{ $product->name }}" class="product-gallery-main rounded-4 w-100">
                        @endif
                    </div>
                </div>

                <div class="col-lg-6">
                    <p class="product-detail-category mb-2">{{ $product->category->name }}</p>
                    <h1 class="product-detail-name mb-3">{{ $product->name }}</h1>

                    <div class="product-detail-price mb-3">
                        @if ($product->hasDiscount())
                            <span class="price-sale h3 me-2">₹{{ number_format((float) $product->discount_price, 0) }}</span>
                            <span class="price-original h5">₹{{ number_format((float) $product->price, 0) }}</span>
                        @else
                            <span class="price-sale h3">₹{{ number_format((float) $product->price, 0) }}</span>
                        @endif
                    </div>

                    <div class="product-detail-stock mb-3">
                        @if ($product->stock_status === \App\Models\Product::STOCK_AVAILABLE)
                            <span class="badge rounded-pill badge-stock badge-in"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
                        @elseif ($product->stock_status === \App\Models\Product::STOCK_OUT_OF_STOCK)
                            <span class="badge rounded-pill badge-stock badge-out"><i class="bi bi-x-circle-fill me-1"></i>Out of Stock</span>
                        @else
                            <span class="badge rounded-pill badge-stock badge-soon"><i class="bi bi-clock-fill me-1"></i>Coming Soon</span>
                        @endif
                    </div>

                    <p class="product-detail-desc">{{ $product->description }}</p>

                    <ul class="product-detail-list list-unstyled">
                        @if ($product->material)
                            <li><strong>Material:</strong> {{ $product->material }}</li>
                        @endif
                        @if ($product->color)
                            <li><strong>Colour:</strong> {{ $product->color }}</li>
                        @endif
                        @if ($product->available_sizes)
                            <li><strong>Available Sizes:</strong>
                                @foreach ($product->available_sizes as $size)
                                    <span class="product-size-chip">{{ $size }}</span>
                                @endforeach
                            </li>
                        @endif
                    </ul>

                    <div class="product-detail-actions mt-4">
                        <label for="wa_size_select" class="form-label small text-muted">Select a size for your enquiry (optional)</label>
                        <select id="wa_size_select" class="form-select yc-input mb-3" style="max-width: 240px;">
                            <option value="">Select size</option>
                            @foreach ($product->available_sizes ?? [] as $size)
                                <option value="{{ $size }}">{{ $size }}</option>
                            @endforeach
                        </select>

                        <x-whatsapp-button
                            :link="$whatsappLink"
                            id="product-wa-btn"
                            class="btn-lg w-100"
                            data-wa-product="{{ $product->name }}"
                            data-wa-price="{{ number_format((float) $product->sellingPrice(), 0) }}"
                        />

                        @if ($product->stock_status !== \App\Models\Product::STOCK_AVAILABLE)
                            <p class="text-muted small mt-2 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                This item is {{ str_replace('_', ' ', $product->stock_status) }}. Message us to pre-order or be notified.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Enquiry form --}}
            <div class="row mt-5">
                <div class="col-lg-8 offset-lg-2">
                    <div class="yc-enquiry-card rounded-4 p-4 p-md-5">
                        <h2 class="yc-enquiry-title text-center mb-1">Send an Enquiry</h2>
                        <p class="text-center text-muted mb-4">We usually reply within a few hours.</p>

                        <x-alert type="success" :message="session('success')" />

                        @if (session('whatsappLink'))
                            <div class="text-center mb-4">
                                <a href="{{ session('whatsappLink') }}" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp">
                                    <i class="bi bi-whatsapp me-2"></i>
                                    Chat with us on WhatsApp
                                </a>
                                <small class="d-block text-muted mt-2">Or continue chatting on WhatsApp for a faster reply.</small>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('enquiry.store') }}" class="row g-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" class="form-control yc-input" required>
                                @error('customer_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="form-control yc-input" placeholder="+91 98765 43210" required>
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="selected_size" class="form-label">Preferred Size</label>
                                <select name="selected_size" id="selected_size" class="form-select yc-input">
                                    <option value="">Select size</option>
                                    @foreach ($product->available_sizes ?? [] as $size)
                                        <option value="{{ $size }}" @selected(old('selected_size') === $size)>{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="enquiry_product" class="form-label">Product</label>
                                <input type="text" class="form-control yc-input" value="{{ $product->name }} — ₹{{ number_format((float) $product->sellingPrice(), 0) }}" disabled>
                            </div>

                            <div class="col-12">
                                <label for="message" class="form-label">Message</label>
                                <textarea name="message" id="message" rows="3" class="form-control yc-input" placeholder="Any questions about this product?">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-yc btn-lg px-5">Send Enquiry</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Related products --}}
    @if ($relatedProducts->isNotEmpty())
        <section class="yc-section yc-section-cream">
            <div class="container">
                <div class="yc-section-head text-center mb-4">
                    <h2 class="yc-section-title">You May Also Like</h2>
                </div>
                <div class="row g-4">
                    @foreach ($relatedProducts as $related)
                        <div class="col-6 col-md-4 col-lg-3">
                            <x-product-card :product="$related" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection