<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEnquiryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    public function test_enquiries_index_renders(): void
    {
        $enquiry = Enquiry::factory()->create();

        $this->get(route('admin.enquiries.index'))
            ->assertOk()
            ->assertSee($enquiry->customer_name);
    }

    public function test_enquiries_can_be_filtered_by_status(): void
    {
        Enquiry::factory()->create(['status' => Enquiry::STATUS_NEW]);
        $contacted = Enquiry::factory()->create(['status' => Enquiry::STATUS_CONTACTED]);

        $this->get(route('admin.enquiries.index', ['status' => Enquiry::STATUS_CONTACTED]))
            ->assertOk()
            ->assertSee($contacted->customer_name);
    }

    public function test_enquiries_can_be_filtered_by_product(): void
    {
        $product = Product::factory()->create();
        $enquiry = Enquiry::factory()->create(['product_id' => $product->id]);
        Enquiry::factory()->create(['product_id' => null]);

        $this->get(route('admin.enquiries.index', ['product_id' => $product->id]))
            ->assertOk()
            ->assertSee($enquiry->customer_name);
    }

    public function test_enquiry_show_page_renders_message(): void
    {
        $enquiry = Enquiry::factory()->create(['message' => 'Please reserve size M for me.']);

        $this->get(route('admin.enquiries.show', $enquiry))
            ->assertOk()
            ->assertSee('Please reserve size M for me.');
    }

    public function test_admin_can_update_enquiry_status(): void
    {
        $enquiry = Enquiry::factory()->create(['status' => Enquiry::STATUS_NEW]);

        $this->post(route('admin.enquiries.update-status', $enquiry), [
            '_method' => 'PATCH',
            'status' => Enquiry::STATUS_CONTACTED,
        ])->assertRedirect();

        $this->assertSame(Enquiry::STATUS_CONTACTED, $enquiry->refresh()->status);
    }

    public function test_admin_cannot_set_invalid_enquiry_status(): void
    {
        $enquiry = Enquiry::factory()->create();

        $this->post(route('admin.enquiries.update-status', $enquiry), [
            '_method' => 'PATCH',
            'status' => 'spam',
        ])->assertStatus(422);

        $this->assertSame(Enquiry::STATUS_NEW, $enquiry->refresh()->status);
    }

    public function test_admin_can_delete_enquiry(): void
    {
        $enquiry = Enquiry::factory()->create();

        $this->delete(route('admin.enquiries.destroy', $enquiry))
            ->assertRedirect(route('admin.enquiries.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('enquiries', ['id' => $enquiry->id]);
    }
}
