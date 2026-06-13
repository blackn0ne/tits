<?php

use App\Enums\BlogPostStatus;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\Language;
use App\Models\User;
use App\Services\SlugGenerator;
use Database\Seeders\LanguageSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(LanguageSeeder::class);
    Storage::fake('public');
});

test('guest cannot access blog posts', function () {
    $this->get(route('admin.blog-posts.index'))->assertRedirect(route('login'));
});

test('regular user cannot access blog posts', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.blog-posts.index'))
        ->assertForbidden();
});

test('admin can view blog posts page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.blog-posts.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/Blog/Posts/Index')
            ->has('posts')
        );
});

test('admin can create blog post with kazakh slug', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $kz = Language::query()->where('code', 'kz')->firstOrFail();

    $category = BlogCategory::factory()->create();
    BlogCategoryTranslation::factory()->create([
        'blog_category_id' => $category->id,
        'language_id' => $ru->id,
        'name' => 'Категория',
        'slug' => 'kategoriia',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.blog-posts.store'), [
        'blog_category_id' => $category->id,
        'status' => BlogPostStatus::Published->value,
        'published_at' => '2026-06-13T10:00',
        'banner' => UploadedFile::fake()->image('banner.jpg'),
        'translations' => [
            $ru->id => [
                'language_id' => $ru->id,
                'title' => 'Первая новость',
                'content' => '<p>Текст</p>',
            ],
            $kz->id => [
                'language_id' => $kz->id,
                'title' => 'Бірінші жаңалық',
                'content' => '<p>Мәтін</p>',
            ],
        ],
    ]);

    $response->assertRedirect(route('admin.blog-posts.index'));

    $post = BlogPost::query()->first();
    expect($post)->not->toBeNull();
    expect($post->status)->toBe(BlogPostStatus::Published);
    expect($post->banner_path)->not->toBeNull();

    $kzTranslation = BlogPostTranslation::query()
        ->where('blog_post_id', $post->id)
        ->where('language_id', $kz->id)
        ->first();

    expect($kzTranslation->slug)->not->toBe('');
    expect($kzTranslation->slug)->not->toContain(' ');
});

test('slug generator transliterates kazakh text', function () {
    $slug = app(SlugGenerator::class)->fromText('Қазақша мәтін');

    expect($slug)->not->toBe('');
    expect($slug)->not->toContain(' ');
});

test('admin can delete blog post', function () {
    $admin = User::factory()->admin()->create();
    $post = BlogPost::factory()->create(['user_id' => $admin->id]);

    $this->actingAs($admin)
        ->delete(route('admin.blog-posts.destroy', $post))
        ->assertRedirect(route('admin.blog-posts.index'));

    $this->assertSoftDeleted('blog_posts', ['id' => $post->id]);
});
