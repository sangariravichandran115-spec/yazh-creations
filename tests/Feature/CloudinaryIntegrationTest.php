<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Services\CloudinaryUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CloudinaryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cloudinary_urls_are_served_over_https_with_auto_transformations(): void
    {
        $image = new ProductImage([
            'path' => 'http://res.cloudinary.com/demo/image/upload/v123/yazh/products/kurti.jpg',
        ]);

        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/f_auto,q_auto,c_limit,w_600/v123/yazh/products/kurti.jpg',
            $image->url(600),
        );
    }

    public function test_cloudinary_transformations_are_not_duplicated(): void
    {
        $url = 'https://res.cloudinary.com/demo/image/upload/f_auto,q_auto,w_600/sample.jpg';

        $this->assertSame($url, CloudinaryUrl::transform($url, 800));
    }

    public function test_local_paths_still_resolve_to_public_storage(): void
    {
        $image = new ProductImage(['path' => 'products/kurti.jpg']);

        $this->assertSame(asset('storage/products/kurti.jpg'), $image->url());
    }

    public function test_missing_images_use_the_existing_placeholders(): void
    {
        $this->assertSame(asset('images/placeholder-product.jpg'), (new ProductImage(['path' => null]))->url());
        $this->assertSame(asset('images/placeholder-category.jpg'), (new Category)->imageUrl());
    }

    public function test_product_images_use_the_primary_cloudinary_image(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/front.jpg',
            'is_primary' => false,
            'position' => 1,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'https://res.cloudinary.com/demo/image/upload/v123/yazh/products/back.jpg',
            'is_primary' => true,
            'position' => 2,
        ]);

        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/f_auto,q_auto,c_limit,w_500/v123/yazh/products/back.jpg',
            $product->imageUrl(500),
        );
    }

    public function test_category_images_resolve_cloudinary_urls(): void
    {
        $category = new Category([
            'image' => 'https://res.cloudinary.com/demo/image/upload/v123/yazh/categories/silk.jpg',
        ]);

        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/f_auto,q_auto,c_limit,w_400/v123/yazh/categories/silk.jpg',
            $category->imageUrl(400),
        );
    }

    public function test_story_image_url_resolves_cloudinary_local_and_empty_values(): void
    {
        Setting::set('story_image', 'https://res.cloudinary.com/demo/image/upload/v123/yazh/settings/story.jpg');
        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/f_auto,q_auto,c_limit,w_300/v123/yazh/settings/story.jpg',
            Setting::storyImageUrl(300),
        );

        Setting::set('story_image', 'settings/story.jpg');
        $this->assertSame(asset('storage/settings/story.jpg'), Setting::storyImageUrl());

        Setting::set('story_image', null);
        $this->assertNull(Setting::storyImageUrl());
    }

    public function test_cloudinary_public_id_is_extracted_for_deletion(): void
    {
        $this->assertSame(
            'yazh-creations/products/kurti',
            CloudinaryUrl::publicId('https://res.cloudinary.com/demo/image/upload/v1712345678/yazh-creations/products/kurti.jpg'),
        );

        $this->assertNull(CloudinaryUrl::publicId('products/kurti.jpg'));
    }

    public function test_remote_non_cloudinary_urls_are_left_untouched(): void
    {
        $this->assertSame(
            'https://cdn.example.com/images/kurti.jpg',
            CloudinaryUrl::resolve('https://cdn.example.com/images/kurti.jpg', 600),
        );
    }
}
