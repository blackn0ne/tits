<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\ProjectCategory;
use App\Models\ProjectCategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectCategoryTranslation>
 */
class ProjectCategoryTranslationFactory extends Factory
{
    protected $model = ProjectCategoryTranslation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_category_id' => ProjectCategory::factory(),
            'language_id' => Language::factory(),
            'name' => fake()->words(2, true),
            'slug' => fake()->unique()->slug(),
        ];
    }
}
