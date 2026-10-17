<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->category = Category::factory()->create();

        $this->actingAs($this->admin);
    }

    public function test_create_product_page_renders(): void
    {
        $this->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Add Product');
    }

    public function test_admin_can_create_product(): void
    {
        Storage::fake('public');

        $this->post(route('admin.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'Summer Floral Kurti',
            'description' => 'A breezy cotton kurti for summer days.',
            'price' => 1299,
            'discount_price' => 999,
            'material' => 'Cotton',
            'color' => 'Red',
            'available_sizes' => ['S', 'M', 'L'],
            'stock_status' => Product::STOCK_AVAILABLE,
            'featured' => '1',
            'status' => Product::STATUS_ACTIVE,
            'images' => [UploadedFile::fake()->image('kurti.jpg', 400, 500)],
        ])
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Summer Floral Kurti',
            'category_id' => $this->category->id,
            'slug' => 'summer-floral-kurti',
            'discount_price' => 999,
            'featured' => true,
        ]);

        $this->assertSame(1, Product::where('name', 'Summer Floral Kurti')->first()->images()->count());
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->put(route('admin.products.update', $product), [
            'category_id' => $this->category->id,
            'name' => 'Updated Kurti Name',
            'description' => 'Updated description.',
            'price' => 1499,
            'discount_price' => '',
            'material' => 'Silk Blend',
            'color' => 'Green',
            'available_sizes' => ['M', 'L'],
            'stock_status' => Product::STOCK_AVAILABLE,
            'featured' => '0',
            'status' => Product::STATUS_INACTIVE,
        ])
            ->assertRedirect(route('admin.products.edit', $product))
            ->assertSessionHas('success');

        $product->refresh();

        $this->assertSame('Updated Kurti Name', $product->name);
        $this->assertSame(1499, (int) $product->price);
        $this->assertNull($product->discount_price);
        $this->assertFalse((bool) $product->featured);
        $this->assertSame(Product::STATUS_INACTIVE, $product->status);
        $this->assertSame(['M', 'L'], $product->available_sizes);
    }

    public function test_edit_page_renders_set_primary_actions_for_secondary_images(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $primary = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/front.jpg',
            'is_primary' => true,
            'position' => 1,
        ]);
        $secondary = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/back.jpg',
            'is_primary' => false,
            'position' => 2,
        ]);

        $this->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('Set as primary')
            ->assertSee('form="set-primary-'.$secondary->id.'"', false)
            ->assertSee('form="delete-image-'.$primary->id.'"', false)
            ->assertSee('form="delete-image-'.$secondary->id.'"', false)
            ->assertSee(route('admin.products.images.primary', [$product, $secondary]), false)
            ->assertSee(route('admin.products.images.destroy', [$product, $secondary]), false);
    }

    public function test_admin_can_set_primary_image(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $primary = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/front.jpg',
            'is_primary' => true,
            'position' => 1,
        ]);
        $secondary = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/back.jpg',
            'is_primary' => false,
            'position' => 2,
        ]);

        $this->post(route('admin.products.images.primary', [$product, $secondary]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertTrue($secondary->fresh()->is_primary);
        $this->assertFalse($primary->fresh()->is_primary);
    }

    public function test_admin_can_delete_product_image(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create(['category_id' => $this->category->id]);

        Storage::disk('public')->put('products/back.jpg', 'fake');
        $image = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/back.jpg',
            'is_primary' => true,
            'position' => 1,
        ]);

        $this->delete(route('admin.products.images.destroy', [$product, $image]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('products/back.jpg');
    }

    public function test_deleting_primary_image_reassigns_primary(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $primary = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/front.jpg',
            'is_primary' => true,
            'position' => 1,
        ]);
        $secondary = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/back.jpg',
            'is_primary' => false,
            'position' => 2,
        ]);

        $this->delete(route('admin.products.images.destroy', [$product, $primary]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('product_images', ['id' => $primary->id]);
        $this->assertTrue($secondary->fresh()->is_primary);
    }

    public function test_product_requires_valid_category(): void
    {
        $this->post(route('admin.products.store'), [
            'category_id' => 999999,
            'name' => 'Nope',
            'description' => 'Nope',
            'price' => 100,
            'stock_status' => Product::STOCK_AVAILABLE,
            'status' => Product::STATUS_ACTIVE,
        ])->assertSessionHasErrors('category_id');
    }

    public function test_product_requires_valid_sizes(): void
    {
        $this->post(route('admin.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'Bad Sizes',
            'description' => 'Nope',
            'price' => 100,
            'available_sizes' => ['ZZZ'],
            'stock_status' => Product::STOCK_AVAILABLE,
            'status' => Product::STATUS_ACTIVE,
        ])->assertSessionHasErrors('available_sizes.0');
    }

    public function test_admin_can_trash_and_list_trashed(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);

        $this->get(route('admin.products.trash'))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_admin_can_restore_trashed_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);
        $product->delete();

        $this->post(route('admin.products.restore', $product))
            ->assertRedirect(route('admin.products.trash'));

        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_admin_can_force_delete_trashed_product(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        Storage::disk('public')->put('products/test.jpg', 'fake');
        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/test.jpg',
            'is_primary' => true,
            'position' => 1,
        ]);

        $product->delete();

        $this->delete(route('admin.products.force-delete', $product))
            ->assertRedirect(route('admin.products.trash'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_images', ['product_id' => $product->id]);
        Storage::disk('public')->assertMissing('products/test.jpg');
    }

    public function test_guest_cannot_access_admin_products(): void
    {
        $this->post(route('logout'));

        $this->get(route('admin.products.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_product_show_page_renders_enquiries(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $this->get(route('admin.products.show', $product))
            ->assertOk()
            ->assertSee($product->name);
    }
}
