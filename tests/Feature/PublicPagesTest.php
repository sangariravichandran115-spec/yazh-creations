<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\View\Components\WhatsAppButton;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->seed(SettingSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($category->name);
    }

    public function test_about_page_loads(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Our Story');
    }

    public function test_home_story_section_uses_settings(): void
    {
        Setting::set('story_title', 'Our Journey');
        Setting::set('about_text', 'A story paragraph.');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Our Journey')
            ->assertSee('A story paragraph.');
    }

    public function test_about_page_story_uses_settings(): void
    {
        Setting::set('story_title', 'Our Journey');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Our Journey');
    }

    public function test_contact_page_loads(): void
    {
        $this->get(route('contact.index'))
            ->assertOk()
            ->assertSee('Contact Us');
    }

    public function test_products_index_page_lists_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(6)->create(['category_id' => $category->id]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee($category->name);
    }

    public function test_product_show_page_loads_for_active_product(): void
    {
        $product = Product::factory()->create();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('Enquire on WhatsApp');
    }

    public function test_inactive_product_returns_404(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_category_page_loads(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->get(route('categories.show', $category))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($category->name);
    }

    public function test_inactive_category_returns_404(): void
    {
        $category = Category::factory()->create(['status' => Category::STATUS_INACTIVE]);

        $this->get(route('categories.show', $category))->assertNotFound();
    }

    public function test_sitemap_returns_xml(): void
    {
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->get(route('sitemap'));

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<urlset', false)
            ->assertSee(route('categories.show', $category), false)
            ->assertSee(route('products.show', $products->first()), false);

        $this->assertStringStartsWith('<?xml', $response->getContent());
    }

    public function test_whatsapp_component_alias_is_registered(): void
    {
        $this->assertSame(
            WhatsAppButton::class,
            Blade::getClassComponentAliases()['whatsapp-button'] ?? null,
        );
    }

    public function test_navigation_cache_stores_plain_arrays(): void
    {
        Category::factory()->create();

        Category::nav();

        $this->assertIsArray(Cache::get(Category::NAV_CACHE_KEY));
    }

    public function test_missing_page_returns_404(): void
    {
        $this->get('/this-page-does-not-exist')->assertNotFound();
    }

    public function test_navigation_uses_settings_for_brand_name(): void
    {
        Setting::set('brand_name', 'Test Brand');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Test Brand');
    }
}
