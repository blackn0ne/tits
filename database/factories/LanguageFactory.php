<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Language>
 */
class LanguageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->languageCode(),
        ];
    }

    public function russian(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Русский',
            'code' => 'ru',
        ]);
    }

    public function kazakh(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Қазақша',
            'code' => 'kz',
        ]);
    }
}
