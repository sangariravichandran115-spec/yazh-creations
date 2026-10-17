@extends('layouts.admin')

@section('title', 'Edit ' . $product->name . ' — Admin')

@section('content')
    <div class="d-flex align-items-center gap-2 mb-4">
        <h1 class="admin-page-title mb-0">Edit Product</h1>
        <span class="badge rounded-pill bg-gold text-white text-truncate" style="max-width: 260px;">{{ $product->name }}</span>
        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-secondary ms-auto"><i class="bi bi-eye me-1"></i>View</a>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products._form', ['product' => $product])
    </form>
@endsection