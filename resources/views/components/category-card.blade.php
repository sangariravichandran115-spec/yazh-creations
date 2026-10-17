<div {{ $attributes->merge(['class' => 'category-card rounded-4 overflow-hidden']) }}>
    <a href="{{ route('categories.show', $category) }}" class="d-block category-card-link">
        @if ($category->image)
            <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" class="category-card-image" loading="lazy">
        @else
            <img src="{{ asset('images/placeholder-category.jpg') }}" alt="{{ $category->name }}" class="category-card-image" loading="lazy">
        @endif
        <div class="category-card-overlay d-flex flex-column align-items-center justify-content-center text-center">
            <h3 class="category-card-name mb-1">{{ $category->name }}</h3>
            @if (isset($productCount))
                <span class="category-card-count small">{{ $productCount }} product{{ $productCount === 1 ? '' : 's' }}</span>
            @endif
            <span class="category-card-link-cta mt-2">Shop Now</span>
        </div>
    </a>
</div>