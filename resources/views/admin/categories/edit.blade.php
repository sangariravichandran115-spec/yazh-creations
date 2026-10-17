@extends('layouts.admin')

@section('title', 'Edit ' . $category->name . ' — Admin')

@section('content')
    <h1 class="admin-page-title mb-4">Edit Category</h1>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="admin-panel rounded-4 p-3">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control yc-input" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control yc-input">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select yc-input">
                            @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $category->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-yc">Update Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="admin-panel rounded-4 p-3">
                    <h2 class="admin-panel-title mb-3">Image</h2>

                    @if ($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="rounded-3 mb-3 w-100">
                    @endif

                    <label for="image" class="form-label d-block">Replace Image <small class="text-muted">(JPG, PNG or WebP, max 5MB)</small></label>
                    <input type="file" name="image" id="image" class="form-control yc-input" accept="image/jpeg,image/png,image/webp">
                    @error('image')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </form>
@endsection