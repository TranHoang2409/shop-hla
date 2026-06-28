<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => 'TEST-' . fake()->unique()->numerify('####'),
            'name' => fake()->words(3, true),
            'category' => fake()->randomElement(array_keys(Product::CATEGORIES)),
            'image' => 'assets/img/banner_img_01.jpg',
            'price' => fake()->numberBetween(200000, 3000000),
            'stock' => fake()->numberBetween(1, 50),
            'is_active' => true,
            'description' => fake()->sentence(),
        ];
    }
}
