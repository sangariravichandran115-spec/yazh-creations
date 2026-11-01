<div {{ $attributes->merge(['class' => 'category-card rounded-4 overflow-hidden']) }}>
    <a href="{{ route('categories.show', $category) }}" class="d-block category-card-link">
        <img src="{{ $category->imageUrl(600) }}"
             srcset="{{ $category->imageUrl(400) }} 400w, {{ $category->imageUrl(600) }} 600w"
             sizes="(min-width: 992px) 25vw, (min-width: 768px) 33vw, 50vw"
             alt="{{ $category->name }} category"
             class="category-card-image"
             loading="lazy"
             decoding="async"
             data-fallback="{{ asset('images/placeholder-category.jpg') }}">
        <div class="category-card-overlay d-flex flex-column align-items-center justify-content-center text-center">
            <h3 class="category-card-name mb-1">{{ $category->name }}</h3>
            @if (isset($productCount))
                <span class="category-card-count small">{{ $productCount }} product{{ $productCount === 1 ? '' : 's' }}</span>
            @endif
            <span class="category-card-link-cta mt-2">Shop Now</span>
        </div>
    </a>
</div>