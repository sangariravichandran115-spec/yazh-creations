<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Sample product catalogue.
     *
     * @return array<int, array<string, mixed>>
     */
    private function catalogue(): array
    {
        return [
            [
                'category' => 'Kurtis', 'name' => 'Anarkali Floral Kurti', 'price' => 1499, 'discount_price' => 1299,
                'material' => 'Rayon', 'color' => 'Burgundy', 'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'stock' => 'available', 'featured' => true, 'description' => 'Graceful Anarkali kurti with a delicate floral print, flowy silhouette and premium stitching.',
            ],
            [
                'category' => 'Kurtis', 'name' => 'Cotton Straight Kurti', 'price' => 1099, 'discount_price' => null,
                'material' => 'Cotton', 'color' => 'Ivory', 'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                'stock' => 'available', 'featured' => false, 'description' => 'Everyday cotton straight kurti with a clean finish and breathable fabric.',
            ],
            [
                'category' => 'Kurtis', 'name' => 'Chikankari Embroidered Kurti', 'price' => 1799, 'discount_price' => 1599,
                'material' => 'Cotton', 'color' => 'Cream', 'sizes' => ['S', 'M', 'L', 'XL'],
                'stock' => 'coming_soon', 'featured' => false, 'description' => 'Hand-embroidered Chikankari kurti in soft cream with subtle gold threadwork.',
            ],
            [
                'category' => 'Leggings', 'name' => 'High Waist Cotton Leggings', 'price' => 599, 'discount_price' => null,
                'material' => 'Cotton Lycra', 'color' => 'Black', 'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'stock' => 'available', 'featured' => false, 'description' => 'Comfortable high-waist leggings with stretch for all-day wear.',
            ],
            [
                'category' => 'Leggings', 'name' => 'Jacquard Textured Leggings', 'price' => 699, 'discount_price' => 549,
                'material' => 'Jacquard', 'color' => 'Wine', 'sizes' => ['S', 'M', 'L', 'XL'],
                'stock' => 'out_of_stock', 'featured' => false, 'description' => 'Richly textured jacquard leggings with an elegant sheen.',
            ],
            [
                'category' => 'Palazzo', 'name' => 'Flared Cotton Palazzo', 'price' => 999, 'discount_price' => null,
                'material' => 'Cotton', 'color' => 'Terracotta', 'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'stock' => 'available', 'featured' => false, 'description' => 'Breathable flared palazzo pants with a relaxed elegant drape.',
            ],
            [
                'category' => 'Palazzo', 'name' => 'Printed Ankle-Length Palazzo', 'price' => 1199, 'discount_price' => 999,
                'material' => 'Crepe', 'color' => 'Burgundy', 'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'stock' => 'available', 'featured' => true, 'description' => 'Lightweight ankle-length palazzos with a stylish global print.',
            ],
            [
                'category' => 'Aari Work', 'name' => 'Aari Work Designer Kurti', 'price' => 1999, 'discount_price' => 1799,
                'material' => 'Silk Blend', 'color' => 'Rose Gold', 'sizes' => ['S', 'M', 'L', 'XL'],
                'stock' => 'available', 'featured' => true, 'description' => 'Signature Aari work kurti with intricate hand-embroidered detailing and zari accents.',
            ],
            [
                'category' => 'Aari Work', 'name' => 'Aari Work Bridal Blouse', 'price' => 2499, 'discount_price' => null,
                'material' => 'Raw Silk', 'color' => 'Maroon', 'sizes' => ['M', 'L', 'XL', 'Free Size'],
                'stock' => 'available', 'featured' => false, 'description' => 'Exquisite bridal blouse with dense Aari embroidery and pearl motifs.',
            ],
            [
                'category' => 'Aari Work', 'name' => 'Aari Work Potli Bag', 'price' => 1299, 'discount_price' => 1099,
                'material' => 'Velvet', 'color' => 'Wine', 'sizes' => ['Free Size'],
                'stock' => 'available', 'featured' => false, 'description' => 'Handcrafted velvet potli bag finished with Aari work and golden tassels.',
            ],
            [
                'category' => 'Handmade Accessories', 'name' => 'Handcrafted Beaded Earrings', 'price' => 449, 'discount_price' => null,
                'material' => 'Beads', 'color' => 'Gold', 'sizes' => ['Free Size'],
                'stock' => 'available', 'featured' => true, 'description' => 'Lightweight hand-strung beaded earrings to elevate any outfit.',
            ],
            [
                'category' => 'Handmade Accessories', 'name' => 'Silk Thread Bangle Set', 'price' => 549, 'discount_price' => 449,
                'material' => 'Silk', 'color' => 'Multicolor', 'sizes' => ['Free Size'],
                'stock' => 'out_of_stock', 'featured' => false, 'description' => 'Set of six silk thread bangles in festive colourways.',
            ],
            [
                'category' => 'Handmade Accessories', 'name' => 'Handwoven Potli Bag', 'price' => 899, 'discount_price' => null,
                'material' => 'Jute', 'color' => 'Natural', 'sizes' => ['Free Size'],
                'stock' => 'coming_soon', 'featured' => false, 'description' => 'Eco-friendly handwoven potli bag perfect for gifting and festive looks.',
            ],
        ];
    }

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

        foreach ($this->catalogue() as $item) {
            $category = Category::where('name', $item['category'])->first();

            if ($category === null) {
                continue;
            }

            $product = Product::firstOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'discount_price' => $item['discount_price'],
                    'material' => $item['material'],
                    'color' => $item['color'],
                    'available_sizes' => $item['sizes'],
                    'stock_status' => $item['stock'],
                    'featured' => $item['featured'],
                    'status' => Product::STATUS_ACTIVE,
                ],
            );

            if ($product->wasRecentlyCreated) {
                $background = $palettes[$item['category']];

                $this->createImages($product, $item['name'], $background, $images);
            }
        }
    }

    /**
     * @param  array{red: int, green: int, blue: int}  $background
     */
    private function createImages(Product $product, string $label, array $background, ImageService $images): void
    {
        for ($index = 0; $index < 3; $index++) {
            $path = $images->createPlaceholder(
                $label.($index > 0 ? ' #'.($index + 1) : ''),
                $background,
                'products',
                'product',
            );

            if ($path === '') {
                continue;
            }

            $product->images()->create([
                'path' => $path,
                'is_primary' => $index === 0,
                'position' => $index + 1,
            ]);
        }
    }
}
