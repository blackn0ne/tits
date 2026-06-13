<?php

namespace Database\Factories;

use App\Enums\SalesCatalogStatus;
use App\Models\SalesProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesProduct>
 */
class SalesProductFactory extends Factory
{
    protected $model = SalesProduct::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'short_description' => fake()->optional()->sentence(),
            'price' => fake()->randomFloat(2, 1000, 500000),
            'quantity' => fake()->numberBetween(0, 100),
            'unit' => 'шт',
            'status' => SalesCatalogStatus::Active,
            'kaspi_link' => fake()->optional()->url(),
            'image_path' => null,
        ];
    }
}
