<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudinaryUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->category = Category::factory()->create();

        Storage::fake('public');

        config()->set('services.cloudinary', [
            'cloud_name' => 'test-cloud',
            'api_key' => 'test-key',
            'api_secret' => 'test-secret',
            'folder' => 'yazh-creations',
            'enabled' => true,
        ]);

        $this->actingAs($this->admin);
    }

    public function test_admin_uploads_product_images_to_cloudinary_and_selects_the_primary(): void
    {
        $this->fakeCloudinary();

        $this->post(route('admin.products.store'), $this->productPayload([
            'images' => [
                UploadedFile::fake()->image('front.jpg', 800, 1000),
                UploadedFile::fake()->image('back.jpg', 800, 1000),
            ],
            'primary_image_index' => '1',
        ]))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHasNoErrors();

        $product = Product::where('name', 'Cloudinary Kurti')->firstOrFail();
        $images = $product->images()->orderBy('position')->get();

        $this->assertCount(2, $images);
        $this->assertStringContainsString('res.cloudinary.com/test-cloud/image/upload/', $images[0]->path);
        $this->assertStringContainsString('res.cloudinary.com/test-cloud/image/upload/', $images[1]->path);
        $this->assertFalse($images[0]->is_primary);
        $this->assertTrue($images[1]->is_primary);

        Http::assertSent(function (Request $request): bool {
            if (! str_ends_with($request->url(), '/image/upload')) {
                return false;
            }

            $params = [
                'folder' => $this->field($request, 'folder'),
                'timestamp' => $this->field($request, 'timestamp'),
            ];

            ksort($params);

            $signature = sha1(http_build_query($params, '', '&', PHP_QUERY_RFC3986).'test-secret');

            return $this->field($request, 'folder') === 'yazh-creations/products'
                && $this->field($request, 'api_key') === 'test-key'
                && $this->field($request, 'signature') === $signature;
        });
    }

    public function test_product_upload_falls_back_to_local_storage_when_cloudinary_fails(): void
    {
        Http::fake([
            'api.cloudinary.com/*' => Http::response(['error' => ['message' => 'Upload failed']], 500),
        ]);

        $this->post(route('admin.products.store'), $this->productPayload([
            'images' => [UploadedFile::fake()->image('front.jpg', 800, 1000)],
        ]))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHasNoErrors();

        $image = Product::where('name', 'Cloudinary Kurti')->firstOrFail()->images()->firstOrFail();

        $this->assertStringStartsWith('products/', $image->path);
        Storage::disk('public')->assertExists($image->path);
    }

    public function test_deleting_a_product_image_destroys_the_cloudinary_asset(): void
    {
        $this->fakeCloudinary();

        $product = Product::factory()->create(['category_id' => $this->category->id]);
        $image = ProductImage::create([
            'product_id' => $product->id,
            'path' => 'https://res.cloudinary.com/test-cloud/image/upload/v123/yazh-creations/products/sample.jpg',
            'is_primary' => true,
            'position' => 1,
        ]);

        $this->delete(route('admin.products.images.destroy', [$product, $image]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);

        Http::assertSent(function (Request $request): bool {
            return str_ends_with($request->url(), '/image/destroy')
                && $this->field($request, 'public_id') === 'yazh-creations/products/sample'
                && (bool) $this->field($request, 'invalidate') === true;
        });
    }

    public function test_category_images_are_uploaded_to_the_categories_folder(): void
    {
        $this->fakeCloudinary();

        $this->post(route('admin.categories.store'), [
            'name' => 'Cloudinary Category',
            'description' => 'Category description.',
            'status' => Category::STATUS_ACTIVE,
            'image' => UploadedFile::fake()->image('silk.jpg', 600, 600),
        ])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasNoErrors();

        $category = Category::where('name', 'Cloudinary Category')->firstOrFail();

        $this->assertStringContainsString('res.cloudinary.com/test-cloud/image/upload/', $category->image);

        Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/image/upload')
            && $this->field($request, 'folder') === 'yazh-creations/categories');
    }

    public function test_story_images_are_uploaded_and_removed_from_cloudinary(): void
    {
        $this->fakeCloudinary();

        $this->patch(route('admin.settings.update'), [
            'brand_name' => 'Yazh Creations',
            'story_image' => UploadedFile::fake()->image('story.jpg', 900, 600),
        ])
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHasNoErrors();

        $this->assertStringContainsString(
            'res.cloudinary.com/test-cloud/image/upload/',
            (string) Setting::get('story_image'),
        );

        Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/image/upload')
            && $this->field($request, 'folder') === 'yazh-creations/settings');

        $this->patch(route('admin.settings.update'), [
            'brand_name' => 'Yazh Creations',
            'remove_story_image' => '1',
        ])
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHasNoErrors();

        $this->assertNull(Setting::get('story_image'));

        Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/image/destroy')
            && $this->field($request, 'public_id') === 'yazh-creations/settings/sample-1');
    }

    private function fakeCloudinary(): void
    {
        $uploadCount = 0;

        Http::fake(function (Request $request) use (&$uploadCount) {
            if (str_ends_with($request->url(), '/image/upload')) {
                $uploadCount++;
                $folder = $this->field($request, 'folder');

                return Http::response([
                    'secure_url' => "https://res.cloudinary.com/test-cloud/image/upload/v123/{$folder}/sample-{$uploadCount}.jpg",
                    'public_id' => "{$folder}/sample-{$uploadCount}",
                ]);
            }

            if (str_ends_with($request->url(), '/image/destroy')) {
                return Http::response(['result' => 'ok']);
            }

            return Http::response(['error' => ['message' => 'Unexpected request']], 500);
        });
    }

    private function field(Request $request, string $name): mixed
    {
        foreach ($request->data() as $part) {
            if (is_array($part) && ($part['name'] ?? null) === $name) {
                return $part['contents'] ?? null;
            }
        }

        return $request[$name] ?? null;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function productPayload(array $overrides = []): array
    {
        return array_merge([
            'category_id' => $this->category->id,
            'name' => 'Cloudinary Kurti',
            'description' => 'A kurti uploaded through Cloudinary.',
            'price' => 1299,
            'material' => 'Cotton',
            'color' => 'Red',
            'available_sizes' => ['S', 'M'],
            'stock_status' => Product::STOCK_AVAILABLE,
            'featured' => '1',
            'status' => Product::STATUS_ACTIVE,
        ], $overrides);
    }
}
