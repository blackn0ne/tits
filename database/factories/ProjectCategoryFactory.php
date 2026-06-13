<?php

namespace Database\Factories;

use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectCategory>
 */
class ProjectCategoryFactory extends Factory
{
    protected $model = ProjectCategory::class;

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
