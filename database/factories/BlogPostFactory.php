<?php

namespace Database\Factories;

use App\Enums\BlogPostStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'blog_category_id' => BlogCategory::factory(),
            'user_id' => User::factory(),
            'banner_path' => null,
            'status' => BlogPostStatus::Draft,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => BlogPostStatus::Published,
            'published_at' => now(),
        ]);
    }
}
