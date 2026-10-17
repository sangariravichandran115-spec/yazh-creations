@extends('layouts.admin')

@section('title', 'Enquiry — ' . $enquiry->customer_name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="admin-page-title mb-0">Enquiry Details</h1>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry) }}" onsubmit="return confirm('Delete this enquiry permanently?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
            </form>
            <a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-panel rounded-4 p-3">
                <h2 class="admin-panel-title mb-3">Enquiry</h2>

                <table class="table admin-detail-table">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <td>{{ $enquiry->customer_name }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>
                                @if ($enquiry->phone)
                                    <a href="tel:{{ $enquiry->phone }}">{{ $enquiry->phone }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Product</th>
                            <td>
                                @if ($enquiry->product)
                                    <a href="{{ route('admin.products.show', $enquiry->product) }}" class="text-decoration-none">{{ $enquiry->product->name }}</a>
                                @else
                                    <span class="text-muted">General enquiry (no product selected)</span>
                                @endif
                            </td>
                        </tr>
                        @if ($enquiry->selected_size)
                            <tr>
                                <th>Preferred Size</th>
                                <td><span class="product-size-chip">{{ $enquiry->selected_size }}</span></td>
                            </tr>
                        @endif
                        <tr>
                            <th>Received</th>
                            <td>{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </tbody>
                </table>

                <h3 class="admin-panel-title fs-6">Message</h3>
                <p class="admin-message-box rounded-3 p-3">{{ $enquiry->message ?: '—' }}</p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-panel rounded-4 p-3">
                <h2 class="admin-panel-title mb-3">Update Status</h2>

                <form method="POST" action="{{ route('admin.enquiries.update-status', $enquiry) }}" class="row g-2">
                    @csrf
                    @method('PATCH')
                    <div class="col-12">
                        <select name="status" class="form-select yc-input">
                            @foreach (\App\Models\Enquiry::STATUSES as $status)
                                <option value="{{ $status }}" @selected($enquiry->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 d-grid">
                        <button type="submit" class="btn btn-yc">Save Status</button>
                    </div>
                </form>
            </div>

            @if ($enquiry->phone)
                <div class="admin-panel rounded-4 p-3 mt-4">
                    <h2 class="admin-panel-title mb-3">Quick Actions</h2>
                    <a href="tel:{{ $enquiry->phone }}" class="btn btn-outline-yc w-100 mb-2"><i class="bi bi-telephone me-1"></i>Call {{ $enquiry->customer_name }}</a>
                    <a href="sms:{{ $enquiry->phone }}" class="btn btn-outline-yc w-100"><i class="bi bi-chat-dots me-1"></i>Text Message</a>
                </div>
            @endif
        </div>
    </div>
@endsection