@extends('layouts.admin')

@section('title', 'Add Product — Admin')

@section('content')
    <div class="d-flex align-items-center gap-2 mb-4">
        <h1 class="admin-page-title mb-0">Add Product</h1>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form', ['product' => null])
    </form>
@endsection