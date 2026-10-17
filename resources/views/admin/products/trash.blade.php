@extends('layouts.admin')

@section('title', 'Trash — Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Trash</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Products</a>
    </div>

    <div class="admin-panel rounded-4 p-3 mb-3">
        <form method="GET" action="{{ route('admin.products.trash') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-8">
                <input type="text" name="search" value="{{ $products->isEmpty() ? request('search') : '' }}" class="form-control yc-input" placeholder="Search trashed products...">
            </div>
            <div class="col-12 col-md-4 d-grid">
                <button type="submit" class="btn btn-yc">Search</button>
            </div>
        </form>
    </div>

    <div class="admin-panel rounded-4 p-3">
        @if ($products->isEmpty())
            <p class="text-muted mb-0">Trash is empty.</p>
        @else
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Deleted At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td class="text-muted small">{{ $product->deleted_at->format('d M Y, h:i A') }}</td>
                                <td class="text-end text-nowrap">
                                    <form method="POST" action="{{ route('admin.products.restore', $product->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.products.force-delete', $product->id) }}" class="d-inline" onsubmit="return confirm('Permanently delete this product and all its images? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Delete Forever</button>
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