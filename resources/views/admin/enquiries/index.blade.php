@extends('layouts.admin')

@section('title', 'Enquiries — Admin')

@section('content')
    <h1 class="admin-page-title mb-4">Enquiries</h1>

    <div class="admin-panel rounded-4 p-3 mb-3">
        <form method="GET" action="{{ route('admin.enquiries.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <input type="text" name="search" value="{{ $search }}" class="form-control yc-input" placeholder="Search by name, phone or message...">
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select yc-input">
                    <option value="">All Statuses</option>
                    @foreach (\App\Models\Enquiry::STATUSES as $status)
                        <option value="{{ $status }}" @selected($filterStatus === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 d-grid">
                <div class="d-grid" style="grid-template-columns: 1fr auto;">
                    <button type="submit" class="btn btn-yc me-2">Filter</button>
                    <a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="admin-panel rounded-4 p-3">
        @if ($enquiries->isEmpty())
            <p class="text-muted mb-0">No enquiries found.</p>
        @else
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($enquiries as $enquiry)
                            <tr>
                                <td>
                                    <strong>{{ $enquiry->customer_name }}</strong>
                                </td>
                                <td>
                                    @if ($enquiry->phone)
                                        <a href="tel:{{ $enquiry->phone }}" class="text-decoration-none text-nowrap">{{ $enquiry->phone }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($enquiry->product)
                                        <a href="{{ route('admin.products.show', $enquiry->product) }}" class="text-decoration-none">{{ Str::limit($enquiry->product->name, 30) }}</a>
                                    @else
                                        <span class="text-muted">General</span>
                                    @endif
                                </td>
                                <td>{{ $enquiry->selected_size ?? '—' }}</td>
                                <td class="text-muted small" style="max-width: 220px;">{{ Str::limit($enquiry->message ?? '', 60) }}</td>
                                <td>
                                    <span class="badge rounded-pill badge-enquiry-{{ $enquiry->status }}">{{ ucfirst($enquiry->status) }}</span>
                                </td>
                                <td class="text-muted small">{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-yc">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($enquiries->hasPages())
                <x-pagination :paginator="$enquiries" />
            @endif
        @endif
    </div>
@endsection