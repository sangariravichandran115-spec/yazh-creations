@extends('layouts.admin')

@section('title', 'Categories — Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Categories</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-yc"><i class="bi bi-plus-lg me-1"></i>Add Category</a>
    </div>

    <div class="admin-panel rounded-4 p-3 mb-3">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-8">
                <input type="text" name="search" value="{{ $search }}" class="form-control yc-input" placeholder="Search categories...">
            </div>
            <div class="col-12 col-md-4 d-grid">
                <button type="submit" class="btn btn-yc">Search</button>
            </div>
        </form>
    </div>

    <div class="admin-panel rounded-4 p-3">
        @if ($categories->isEmpty())
            <p class="text-muted mb-0">No categories found.</p>
        @else
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td class="admin-table-thumb">
                                    @if ($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                                    @else
                                        <img src="{{ asset('images/placeholder-category.jpg') }}" alt="{{ $category->name }}">
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('categories.show', $category) }}" target="_blank" class="fw-semibold text-decoration-none">{{ $category->name }}</a>
                                </td>
                                <td class="text-muted small" style="max-width: 240px;">{{ Str::limit($category->description ?? '', 60) }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark">{{ $category->products_count }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill badge-product-{{ $category->status }}">{{ ucfirst($category->status) }}</span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-yc"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Delete this category? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <x-pagination :paginator="$categories" />
            @endif
        @endif
    </div>
@endsection