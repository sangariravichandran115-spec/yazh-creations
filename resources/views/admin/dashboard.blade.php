@extends('layouts.admin')

@section('title', 'Dashboard — Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="admin-page-title mb-0">Dashboard</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-yc"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="admin-stat-card rounded-4 p-3 text-center">
                <span class="admin-stat-label">Total Products</span>
                <span class="admin-stat-value">{{ $totalProducts }}</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="admin-stat-card rounded-4 p-3 text-center">
                <span class="admin-stat-label">Active</span>
                <span class="admin-stat-value text-success">{{ $activeProducts }}</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="admin-stat-card rounded-4 p-3 text-center">
                <span class="admin-stat-label">Out of Stock</span>
                <span class="admin-stat-value text-danger">{{ $outOfStockProducts }}</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="admin-stat-card rounded-4 p-3 text-center">
                <span class="admin-stat-label">Categories</span>
                <span class="admin-stat-value">{{ $totalCategories }}</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="admin-stat-card rounded-4 p-3 text-center">
                <span class="admin-stat-label">New Enquiries</span>
                <span class="admin-stat-value text-warning">{{ $newEnquiries }}</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="admin-stat-card rounded-4 p-3 text-center">
                <span class="admin-stat-label">Total Enquiries</span>
                <span class="admin-stat-value">{{ $totalEnquiries }}</span>
            </div>
        </div>
    </div>

    <div class="admin-panel rounded-4 p-3">
        <h2 class="admin-panel-title mb-3">Recent Enquiries</h2>

        @if ($recentEnquiries->isEmpty())
            <p class="text-muted mb-0">No enquiries yet.</p>
        @else
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentEnquiries as $enquiry)
                            <tr>
                                <td>{{ $enquiry->customer_name }}</td>
                                <td>{{ $enquiry->phone }}</td>
                                <td>
                                    @if ($enquiry->product)
                                        <a href="{{ route('admin.products.show', $enquiry->product) }}">{{ Str::limit($enquiry->product->name, 30) }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill badge-enquiry-{{ $enquiry->status }}">{{ ucfirst($enquiry->status) }}</span>
                                </td>
                                <td class="text-muted small">{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-yc">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection