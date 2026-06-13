<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Project;
use App\Models\ProjectTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectTranslation>
 */
class ProjectTranslationFactory extends Factory
{
    protected $model = ProjectTranslation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'language_id' => Language::factory(),
            'title' => fake()->sentence(4),
            'slug' => fake()->unique()->slug(),
            'content' => '<p>'.fake()->paragraph().'</p>',
        ];
    }
}
