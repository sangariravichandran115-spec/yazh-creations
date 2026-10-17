<div {{ $attributes->merge(['class' => 'product-card rounded-4 overflow-hidden']) }}>
    <a href="{{ route('products.show', $product) }}" class="product-card-image-link d-block position-relative">
        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="product-card-image" loading="lazy">

        @if ($product->stock_status === \App\Models\Product::STOCK_OUT_OF_STOCK)
            <span class="product-badge badge-stock badge-out">Out of Stock</span>
        @elseif ($product->stock_status === \App\Models\Product::STOCK_COMING_SOON)
            <span class="product-badge badge-stock badge-soon">Coming Soon</span>
        @endif

        @if ($product->hasDiscount())
            <span class="product-badge badge-off">Sale</span>
        @endif
    </a>

    <div class="product-card-body p-3 text-center">
        <p class="product-card-category mb-1 small">{{ $product->category->name }}</p>
        <h3 class="product-card-title mb-2">
            <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
        </h3>

        <div class="product-card-price mb-3">
            @if ($product->hasDiscount())
                <span class="price-original">₹{{ number_format((float) $product->price, 0) }}</span>
                <span class="price-sale">₹{{ number_format((float) $product->discount_price, 0) }}</span>
            @else
                <span class="price-sale">₹{{ number_format((float) $product->price, 0) }}</span>
            @endif
        </div>

        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-yc btn-sm w-100">View Product</a>
    </div>
</div>