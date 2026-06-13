<?php

namespace Database\Factories;

use App\Models\SalesClient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesClient>
 */
class SalesClientFactory extends Factory
{
    protected $model = SalesClient::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'phone' => fake()->numerify('+7 7## ### ## ##'),
        ];
    }
}
