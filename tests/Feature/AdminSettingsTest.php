<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    public function test_settings_page_renders_current_values(): void
    {
        Setting::set('brand_name', 'My Brand');

        $this->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('My Brand');
    }

    public function test_admin_can_update_settings(): void
    {
        $this->post(route('admin.settings.update'), [
            '_method' => 'PATCH',
            'brand_name' => 'New Brand',
            'tagline' => 'Tasteful fashion',
            'whatsapp_number' => '+91 91234 56789',
            'instagram_url' => 'https://instagram.com/newbrand',
            'email' => 'hello@newbrand.com',
            'phone' => '+91 91234 56789',
            'address' => 'Chennai, India',
            'about_text' => 'About us.',
            'hero_title' => 'Welcome',
            'hero_description' => 'Fresh designs.',
            'story_title' => 'Our Journey',
        ])
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHas('success');

        $this->assertSame('New Brand', Setting::get('brand_name'));
        $this->assertSame('https://instagram.com/newbrand', Setting::get('instagram_url'));
        $this->assertSame('Welcome', Setting::get('hero_title'));
        $this->assertSame('Our Journey', Setting::get('story_title'));
    }

    public function test_settings_requires_brand_name(): void
    {
        $this->patch(route('admin.settings.update'), [
            'brand_name' => '',
        ])->assertSessionHasErrors('brand_name');
    }

    public function test_admin_can_upload_story_image(): void
    {
        Storage::fake('public');

        $this->patch(route('admin.settings.update'), [
            'brand_name' => 'Yazh Creations',
            'story_title' => 'Our Journey',
            'story_image' => UploadedFile::fake()->image('story.jpg', 800, 600),
        ])
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHas('success');

        $this->assertSame('Our Journey', Setting::get('story_title'));

        $path = Setting::get('story_image');
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_replacing_story_image_deletes_previous_file(): void
    {
        Storage::fake('public');

        $oldPath = UploadedFile::fake()->image('old.jpg', 800, 600)->store('settings', 'public');
        Setting::set('story_image', $oldPath);

        $this->patch(route('admin.settings.update'), [
            'brand_name' => 'Yazh Creations',
            'story_image' => UploadedFile::fake()->image('new.jpg', 800, 600),
        ])->assertRedirect(route('admin.settings.index'));

        $newPath = Setting::get('story_image');
        $this->assertNotEmpty($newPath);
        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_admin_can_remove_story_image(): void
    {
        Storage::fake('public');

        $path = UploadedFile::fake()->image('story.jpg', 800, 600)->store('settings', 'public');
        Setting::set('story_image', $path);

        $this->patch(route('admin.settings.update'), [
            'brand_name' => 'Yazh Creations',
            'remove_story_image' => '1',
        ])->assertRedirect(route('admin.settings.index'));

        $this->assertNull(Setting::get('story_image'));
        Storage::disk('public')->assertMissing($path);
    }

    public function test_story_image_must_be_an_image(): void
    {
        $this->patch(route('admin.settings.update'), [
            'brand_name' => 'Yazh Creations',
            'story_image' => UploadedFile::fake()->create('story.txt', 10),
        ])->assertSessionHasErrors('story_image');
    }
}
