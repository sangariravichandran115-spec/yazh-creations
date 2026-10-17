<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Default business settings.
     *
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'brand_name' => 'Yazh Creations',
            'tagline' => 'Wear • Create • Inspire',
            'whatsapp_number' => '+91 98765 43210',
            'instagram_url' => 'https://instagram.com/yazhcreations',
            'email' => 'hello@yazhcreations.com',
            'phone' => '+91 98765 43210',
            'address' => 'Coimbatore, Tamil Nadu, India',
            'about_text' => 'Yazh Creations is a women\'s fashion and handcrafted accessories brand celebrating the art of handmade elegance. From trending kurtis, leggings and palazzos to intricate Aari work and handmade accessories, every piece is crafted with love, tradition and premium quality.',
            'hero_title' => 'Wear • Create • Inspire',
            'hero_description' => 'Elegant women\'s fashion and handcrafted creations. Explore kurtis, leggings, palazzos, Aari work and handmade accessories made with love in India.',
            'story_title' => 'Our Story',
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::defaults() as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
