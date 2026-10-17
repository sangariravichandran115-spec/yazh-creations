<?php

namespace Database\Factories;

use App\Models\Enquiry;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enquiry>
 */
class EnquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'customer_name' => fake()->name(),
            'phone' => fake()->numerify('+91 9########'),
            'selected_size' => fake()->optional(0.6)->randomElement(Product::SIZES),
            'message' => fake()->optional(0.8)->sentence(),
            'status' => Enquiry::STATUS_NEW,
        ];
    }

    /**
     * Indicate the enquiry is a general one without a product.
     */
    public function general(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => null,
        ]);
    }
}
