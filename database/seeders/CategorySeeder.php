<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ImageService $images): void
    {
        $palettes = [
            'Kurtis' => ['red' => 110, 'green' => 23, 'blue' => 58],
            'Leggings' => ['red' => 148, 'green' => 106, 'blue' => 30],
            'Palazzo' => ['red' => 43, 'green' => 96, 'blue' => 84],
            'Aari Work' => ['red' => 120, 'green' => 63, 'blue' => 92],
            'Handmade Accessories' => ['red' => 176, 'green' => 132, 'blue' => 61],
        ];

        foreach ($palettes as $name => $background) {
            $category = Category::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => "Explore our collection of premium {$name}.",
                    'status' => Category::STATUS_ACTIVE,
                ],
            );

            if ($category->wasRecentlyCreated) {
                $category->update([
                    'image' => $images->createPlaceholder(
                        strtoupper($name),
                        $background,
                        'categories',
                        'category',
                    ),
                ]);
            }
        }
    }
}
