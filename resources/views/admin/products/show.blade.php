@extends('layouts.admin')

@section('title', $product->name . ' — Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="admin-page-title mb-0">
            {{ $product->name }}
            <span class="badge rounded-pill badge-product-{{ $product->status }} align-middle ms-1">{{ ucfirst($product->status) }}</span>
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('products.show', $product) }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-eye me-1"></i>View on Site</a>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-yc"><i class="bi bi-pencil me-1"></i>Edit</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-panel rounded-4 p-3 mb-4">
                <h2 class="admin-panel-title mb-3">Images</h2>
                @if ($product->images->isEmpty())
                    <p class="text-muted mb-0">No images uploaded.</p>
                @else
                    <div class="row g-2">
                        @foreach ($product->images as $image)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="admin-image-box rounded-3 {{ $image->is_primary ? 'active' : '' }}">
                                    <img src="{{ $image->url() }}" alt="{{ $product->name }}" class="rounded-3 w-100">
                                    @if ($image->is_primary)
                                        <span class="admin-image-badge badge bg-gold text-white">Primary</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="admin-panel rounded-4 p-3">
                <h2 class="admin-panel-title mb-3">Details</h2>
                <table class="table admin-detail-table">
                    <tbody>
                        <tr>
                            <th>Category</th>
                            <td>{{ $product->category->name }}</td>
                        </tr>
                        <tr>
                            <th>Price</th>
                            <td>
                                @if ($product->hasDiscount())
                                    ₹{{ number_format((float) $product->price, 0) }}
                                    <del class="text-muted">→</del>
                                    <span class="text-danger fw-semibold">₹{{ number_format((float) $product->discount_price, 0) }}</span>
                                @else
                                    ₹{{ number_format((float) $product->price, 0) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Stock Status</th>
                            <td><span class="badge rounded-pill badge-stock badge-{{ $product->stock_status }}">{{ ucwords(str_replace('_', ' ', $product->stock_status)) }}</span></td>
                        </tr>
                        <tr>
                            <th>Featured</th>
                            <td>{{ $product->featured ? 'Yes' : 'No' }}</td>
                        </tr>
                        @if ($product->material)
                            <tr>
                                <th>Material</th>
                                <td>{{ $product->material }}</td>
                            </tr>
                        @endif
                        @if ($product->color)
                            <tr>
                                <th>Colour</th>
                                <td>{{ $product->color }}</td>
                            </tr>
                        @endif
                        @if ($product->available_sizes)
                            <tr>
                                <th>Sizes</th>
                                <td>
                                    @foreach ($product->available_sizes as $size)
                                        <span class="product-size-chip">{{ $size }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th>Slug</th>
                            <td><code>{{ $product->slug }}</code></td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $product->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        @if ($product->updated_at != $product->created_at)
                            <tr>
                                <th>Updated</th>
                                <td>{{ $product->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                @if ($product->description)
                    <h3 class="admin-panel-title fs-6 mt-4">Description</h3>
                    <p class="mb-0">{{ $product->description }}</p>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-panel rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="admin-panel-title mb-0">Enquiries</h2>
                    <a href="{{ route('admin.enquiries.index', ['product_id' => $product->id]) }}" class="small">View all</a>
                </div>

                @if ($product->enquiries->isEmpty())
                    <p class="text-muted mb-0">No enquiries for this product yet.</p>
                @else
                    <ul class="list-unstyled admin-enquiry-list">
                        @foreach ($product->enquiries->sortByDesc('created_at')->take(5) as $enquiry)
                            <li class="mb-3">
                                <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="text-decoration-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>{{ $enquiry->customer_name }}</strong>
                                        <span class="badge rounded-pill badge-enquiry-{{ $enquiry->status }}">{{ ucfirst($enquiry->status) }}</span>
                                    </div>
                                    <div class="small text-muted">{{ $enquiry->phone }} · {{ $enquiry->created_at->format('d M Y') }}</div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection