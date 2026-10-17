<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    public function test_categories_index_renders(): void
    {
        Category::factory()->count(3)->create();

        $this->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Categories');
    }

    public function test_admin_can_create_category(): void
    {
        Storage::fake('public');

        $this->post(route('admin.categories.store'), [
            'name' => 'Handmade Accessories',
            'description' => 'Bags, jewellery and more.',
            'status' => Category::STATUS_ACTIVE,
            'image' => UploadedFile::fake()->image('category.jpg', 800, 600),
        ])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Handmade Accessories',
            'slug' => 'handmade-accessories',
        ]);
    }

    public function test_category_requires_name(): void
    {
        $this->post(route('admin.categories.store'), [
            'name' => '',
        ])->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name', 'slug' => 'old-name']);

        $this->put(route('admin.categories.update', $category), [
            'name' => 'New Name',
            'description' => 'Updated description.',
            'status' => Category::STATUS_INACTIVE,
        ])
            ->assertRedirect(route('admin.categories.edit', $category))
            ->assertSessionHas('success');

        $this->assertSame('New Name', $category->refresh()->name);
        $this->assertSame('old-name', $category->slug);
        $this->assertSame(Category::STATUS_INACTIVE, $category->status);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->delete(route('admin.categories.destroy', $category))
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_empty_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $this->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
