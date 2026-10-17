@extends('layouts.app')

@section('title', 'Products — ' . $brandName)
@section('meta_description', 'Browse premium kurtis, leggings, palazzos, Aari work and handmade accessories from ' . $brandName . '.')

@section('content')
    <section class="yc-page-hero">
        <div class="container py-5 text-center">
            <h1 class="yc-page-title mb-2">Our Collection</h1>
            <p class="yc-page-subtitle mb-0">{{ $tagline }}</p>
        </div>
    </section>

    <section class="yc-section">
        <div class="container">
            <div class="yc-filter-bar rounded-4 p-3 mb-4">
                <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text yc-input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control yc-input" placeholder="Search products...">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="category" class="form-select yc-input">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(($filters['category'] ?? '') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="sort" class="form-select yc-input">
                            <option value="newest" @selected(($filters['sort'] ?? null) === 'newest')>Newest First</option>
                            <option value="price-asc" @selected(($filters['sort'] ?? null) === 'price-asc')>Price: Low to High</option>
                            <option value="price-desc" @selected(($filters['sort'] ?? null) === 'price-desc')>Price: High to Low</option>
                            <option value="featured" @selected(! empty($filters['featured']))>Featured Only</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button type="submit" class="btn btn-yc">Filter</button>
                    </div>
                </form>
            </div>

            @if (empty($filters['search']) && empty($filters['category']) && empty($filters['sort']))
                <p class="text-muted small mb-3">{{ $products->total() }} products</p>
            @else
                <p class="text-muted small mb-3">{{ $products->total() }} product(s) found</p>
            @endif

            @if ($products->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-box-seam yc-empty-icon"></i>
                    <h3 class="mt-3">No products found</h3>
                    <p class="text-muted">Try adjusting your search or filters.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-yc">Clear Filters</a>
                </div>
            @else
                <div class="row g-4">
                    @foreach ($products as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>

                @if ($products->hasPages())
                    <x-pagination :paginator="$products" />
                @endif
            @endif
        </div>
    </section>
@endsection