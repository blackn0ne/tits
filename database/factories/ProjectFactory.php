<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_category_id' => ProjectCategory::factory(),
            'user_id' => User::factory(),
            'banner_path' => null,
            'status' => ProjectStatus::Draft,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => ProjectStatus::Published,
            'published_at' => now(),
        ]);
    }
}
