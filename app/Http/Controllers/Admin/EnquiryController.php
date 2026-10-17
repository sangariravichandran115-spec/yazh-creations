<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $enquiries = Enquiry::query()
            ->with('product:id,name,slug')
            ->when($request->integer('product_id'), function ($query, int $productId): void {
                $query->where('product_id', $productId);
            })
            ->when($request->string('status')->value(), function ($query, string $status): void {
                if (in_array($status, Enquiry::STATUSES, true)) {
                    $query->where('status', $status);
                }
            })
            ->when($request->string('search')->value(), function ($query, string $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('customer_name', 'ilike', "%{$search}%")
                        ->orWhere('phone', 'ilike', "%{$search}%")
                        ->orWhere('message', 'ilike', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.enquiries.index', [
            'enquiries' => $enquiries,
            'filterStatus' => $request->string('status')->value() ?? '',
            'search' => $request->string('search')->value() ?? '',
        ]);
    }

    public function show(Enquiry $enquiry): View
    {
        return view('admin.enquiries.show', [
            'enquiry' => $enquiry,
        ]);
    }

    public function updateStatus(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $status = $request->string('status')->value();

        abort_unless(in_array($status, Enquiry::STATUSES, true), 422);

        $enquiry->update(['status' => $status]);

        return back()->with('success', 'Enquiry status updated to '.ucfirst($status).'.');
    }

    public function destroy(Enquiry $enquiry): RedirectResponse
    {
        $enquiry->delete();

        return redirect()
            ->route('admin.enquiries.index')
            ->with('success', 'Enquiry deleted successfully.');
    }
}
