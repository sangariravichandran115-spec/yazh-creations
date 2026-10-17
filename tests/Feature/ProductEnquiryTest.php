<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductEnquiryTest extends TestCase
{
    use RefreshDatabase;

    private function product(): Product
    {
        return Product::factory()->create([
            'available_sizes' => ['S', 'M', 'L'],
        ]);
    }

    public function test_valid_enquiry_is_stored(): void
    {
        $product = $this->product();

        $response = $this->from(route('products.show', $product))->post(route('enquiry.store'), [
            'customer_name' => 'Priya Sharma',
            'phone' => '+91 98765 43210',
            'product_id' => $product->id,
            'selected_size' => 'M',
            'message' => 'Is this available in blue?',
        ]);

        $response->assertRedirect(route('products.show', $product))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('enquiries', [
            'customer_name' => 'Priya Sharma',
            'phone' => '+91 98765 43210',
            'product_id' => $product->id,
            'selected_size' => 'M',
            'message' => 'Is this available in blue?',
            'status' => Enquiry::STATUS_NEW,
        ]);
    }

    public function test_enquiry_redirect_redirects_back_with_whatsapp_link(): void
    {
        Setting::set('whatsapp_number', '+91 98765 43210');
        $product = $this->product();

        $this->from(route('products.show', $product))->post(route('enquiry.store'), [
            'customer_name' => 'Priya',
            'phone' => '9876543210',
            'product_id' => $product->id,
        ]);

        $this->assertTrue(session()->has('whatsappLink'));
        $this->assertStringContainsString('https://wa.me/919876543210', (string) session('whatsappLink'));
    }

    public function test_enquiry_requires_customer_name_and_phone(): void
    {
        $product = $this->product();

        $this->from(route('products.show', $product))->post(route('enquiry.store'), [
            'product_id' => $product->id,
        ])
            ->assertSessionHasErrors(['customer_name', 'phone']);
    }

    public function test_enquiry_rejects_invalid_phone(): void
    {
        $product = $this->product();

        $this->from(route('products.show', $product))->post(route('enquiry.store'), [
            'customer_name' => 'Priya',
            'phone' => 'abc',
            'product_id' => $product->id,
        ])->assertSessionHasErrors('phone');
    }

    public function test_enquiry_rejects_invalid_size(): void
    {
        $product = $this->product();

        $this->from(route('products.show', $product))->post(route('enquiry.store'), [
            'customer_name' => 'Priya',
            'phone' => '9876543210',
            'product_id' => $product->id,
            'selected_size' => 'ZZZ',
        ])->assertSessionHasErrors('selected_size');
    }

    public function test_enquiry_rejects_missing_product(): void
    {
        $this->from('/')->post(route('enquiry.store'), [
            'customer_name' => 'Priya',
            'phone' => '9876543210',
        ])->assertSessionHasErrors('product_id');
    }
}
