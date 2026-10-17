@php
    $selectedSizes = old('available_sizes', $product->available_sizes ?? []);
    $product = $product ?? null;
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-panel rounded-4 p-3 mb-4">
            <h2 class="admin-panel-title mb-3">Basic Details</h2>

            <div class="mb-3">
                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control yc-input" value="{{ old('name', $product->name ?? '') }}" required>
                @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select yc-input" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id ?? 0) === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug <small class="text-muted">(URL)</small></label>
                        <input type="text" name="slug" id="slug" class="form-control yc-input" value="{{ old('slug', $product->slug ?? '') }}">
                        @error('slug')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="6" class="form-control yc-input">{{ old('description', $product->description ?? '') }}</textarea>
                @error('description')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="admin-panel rounded-4 p-3 mb-4">
            <h2 class="admin-panel-title mb-3">Pricing</h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="price" class="form-control yc-input" min="0" step="0.01" value="{{ old('price', $product->price ?? '') }}" required>
                        @error('price')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="discount_price" class="form-label">Discount Price (₹) <small class="text-muted">(optional)</small></label>
                        <input type="number" name="discount_price" id="discount_price" class="form-control yc-input" min="0" step="0.01" value="{{ old('discount_price', $product->discount_price ?? '') }}">
                        @error('discount_price')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-panel rounded-4 p-3">
            <h2 class="admin-panel-title mb-3">Details</h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="material" class="form-label">Material</label>
                        <input type="text" name="material" id="material" class="form-control yc-input" value="{{ old('material', $product->material ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="color" class="form-label">Colour</label>
                        <input type="text" name="color" id="color" class="form-control yc-input" value="{{ old('color', $product->color ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Available Sizes</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (\App\Models\Product::SIZES as $size)
                        <div>
                            <input type="checkbox" class="btn-check" name="available_sizes[]" id="size_{{ $size }}" value="{{ $size }}"
                                @checked(in_array($size, $selectedSizes))>
                            <label class="btn btn-outline-yc btn-sm" for="size_{{ $size }}">{{ $size }}</label>
                        </div>
                    @endforeach
                </div>
                @error('available_sizes')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="stock_status" class="form-label">Stock Status</label>
                <select name="stock_status" id="stock_status" class="form-select yc-input">
                    @foreach (\App\Models\Product::STOCK_STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(old('stock_status', $product->stock_status ?? \App\Models\Product::STOCK_AVAILABLE) === $value)>{{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-panel rounded-4 p-3 mb-4">
            <h2 class="admin-panel-title mb-3">Status</h2>

            <div class="mb-3">
                <label for="status" class="form-label">Product Status</label>
                <select name="status" id="status" class="form-select yc-input">
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $product->status ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-check form-switch mb-3">
                <input type="hidden" name="featured" value="0">
                <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" @checked((bool) old('featured', $product->featured ?? false))>
                <label class="form-check-label" for="featured">Featured on home page</label>
            </div>

            <button type="submit" class="btn btn-yc w-100">{{ $product ? 'Update Product' : 'Save Product' }}</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
        </div>

        <div class="admin-panel rounded-4 p-3">
            <h2 class="admin-panel-title mb-3">Product Images</h2>

            <div class="mb-2">
                <label for="images" class="form-label d-block">Upload Images <small class="text-muted">(JPG, PNG or WebP, max 5MB each)</small></label>
                <input type="file" name="images[]" id="images" class="form-control yc-input" accept="image/jpeg,image/png,image/webp" multiple>
                @error('images')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('images.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            @if ($product && $product->images->isNotEmpty())
                <div class="row g-2 mt-2">
                    @foreach ($product->images as $image)
                        <div class="col-6">
                            <div class="admin-image-box rounded-3 position-relative {{ $image->is_primary ? 'active' : '' }}">
                                <img src="{{ $image->url() }}" alt="{{ $product->name }}" class="rounded-3 w-100">
                                @if ($image->is_primary)
                                    <span class="admin-image-badge badge bg-gold text-white">Primary</span>
                                @else
                                    <button type="submit" form="set-primary-{{ $image->id }}" class="admin-image-btn btn btn-sm btn-light position-absolute" title="Set as primary">Star</button>
                                @endif
                                <button type="submit" form="delete-image-{{ $image->id }}" class="admin-image-btn btn btn-sm btn-light position-absolute" title="Delete image" style="top: auto; bottom: 0.4rem;"><i class="bi bi-trash"></i></button>
                                @push('forms')
                                    <form id="set-primary-{{ $image->id }}" method="POST" action="{{ route('admin.products.images.primary', [$product, $image]) }}" autocomplete="off">
                                        @csrf
                                    </form>
                                    <form id="delete-image-{{ $image->id }}" method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" autocomplete="off">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endpush
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted small mb-0 mt-2">No images yet. The first uploaded image becomes the primary image.</p>
            @endif
        </div>
    </div>
</div>