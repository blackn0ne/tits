<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlogCategoryTranslation>
 */
class BlogCategoryTranslationFactory extends Factory
{
    protected $model = BlogCategoryTranslation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'blog_category_id' => BlogCategory::factory(),
            'language_id' => Language::factory(),
            'name' => fake()->words(2, true),
            'slug' => fake()->unique()->slug(),
        ];
    }
}
