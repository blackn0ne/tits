<?php

namespace Database\Factories;

use App\Enums\SalesCatalogStatus;
use App\Models\SalesService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesService>
 */
class SalesServiceFactory extends Factory
{
    protected $model = SalesService::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'price' => fake()->randomFloat(2, 5000, 200000),
            'status' => SalesCatalogStatus::Active,
        ];
    }
}
