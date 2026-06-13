<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlogPostTranslation>
 */
class BlogPostTranslationFactory extends Factory
{
    protected $model = BlogPostTranslation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'blog_post_id' => BlogPost::factory(),
            'language_id' => Language::factory(),
            'title' => fake()->sentence(4),
            'slug' => fake()->unique()->slug(),
            'content' => '<p>'.fake()->paragraph().'</p>',
        ];
    }
}
