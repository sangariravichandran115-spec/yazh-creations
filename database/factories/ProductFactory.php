<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'slug' => Str::slug(fake()->unique()->words(4, true)),
            'description' => fake()->paragraph(),
            'price' => $price = fake()->numberBetween(499, 4999),
            'discount_price' => fake()->boolean(65) ? fake()->numberBetween(299, $price - 1) : null,
            'material' => fake()->randomElement(['Cotton', 'Silk Blend', 'Rayon', 'Crepe', 'Georgette']),
            'color' => fake()->safeColorName(),
            'available_sizes' => fake()->randomElements(Product::SIZES, fake()->numberBetween(2, 5)),
            'stock_status' => fake()->randomElement([
                Product::STOCK_AVAILABLE,
                Product::STOCK_OUT_OF_STOCK,
                Product::STOCK_COMING_SOON,
            ]),
            'featured' => fake()->boolean(20),
            'status' => Product::STATUS_ACTIVE,
        ];
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
            'stock_status' => Product::STOCK_AVAILABLE,
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Product::STATUS_INACTIVE,
        ]);
    }
}
