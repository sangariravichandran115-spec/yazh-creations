<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnquiryStoreRequest;
use App\Models\Enquiry;
use App\Models\Product;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;

class EnquiryController extends Controller
{
    public function store(EnquiryStoreRequest $request, WhatsAppService $whatsapp): RedirectResponse
    {
        $validated = $request->safe()->only([
            'customer_name',
            'phone',
            'product_id',
            'selected_size',
            'message',
        ]);

        $enquiry = Enquiry::create($validated);

        $product = Product::find($validated['product_id']);

        $waMessage = $product
            ? $whatsapp->buildMessage($product, $validated['selected_size'] ?? null, $validated['customer_name'], $validated['phone'], $validated['message'] ?? null)
            : null;

        return back()
            ->with('success', 'Thank you '.$validated['customer_name'].'! Your enquiry has been received. We will get back to you shortly.')
            ->with('enquiry', $enquiry)
            ->with('whatsappLink', $waMessage ? $whatsapp->link($waMessage) : null);
    }
}
