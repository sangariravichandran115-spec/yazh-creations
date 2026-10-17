@extends('layouts.admin')

@section('title', 'Products — Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Products</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.trash') }}" class="btn btn-outline-secondary"><i class="bi bi-trash me-1"></i>Trash</a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-yc"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
        </div>
    </div>

    <div class="admin-panel rounded-4 p-3 mb-3">
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control yc-input" placeholder="Search by name, material, colour...">
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
                <select name="status" class="form-select yc-input">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-grid">
                <button type="submit" class="btn btn-yc">Filter</button>
            </div>
        </form>
    </div>

    <div class="admin-panel rounded-4 p-3">
        @if ($products->isEmpty())
            <p class="text-muted mb-0">No products found.</p>
        @else
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="admin-table-thumb">
                                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy">
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.show', $product) }}" class="fw-semibold text-decoration-none">{{ $product->name }}</a>
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td>
                                    @if ($product->hasDiscount())
                                        <del class="text-muted small">₹{{ number_format((float) $product->price, 0) }}</del>
                                        <span class="text-danger fw-semibold">₹{{ number_format((float) $product->discount_price, 0) }}</span>
                                    @else
                                        ₹{{ number_format((float) $product->price, 0) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill badge-stock badge-{{ $product->stock_status }}">{{ ucwords(str_replace('_', ' ', $product->stock_status)) }}</span>
                                </td>
                                <td>
                                    @if ($product->featured)
                                        <span class="badge bg-gold text-white rounded-pill">Featured</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill badge-product-{{ $product->status }}">{{ ucfirst($product->status) }}</span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-yc" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Move this product to trash?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Trash"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <x-pagination :paginator="$products" />
            @endif
        @endif
    </div>
@endsection