<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlogCategory>
 */
class BlogCategoryFactory extends Factory
{
    protected $model = BlogCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'icon' => 'Folder',
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}
