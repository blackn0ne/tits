<?php

use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\Language;
use App\Models\User;
use Database\Seeders\LanguageSeeder;

beforeEach(function () {
    $this->seed(LanguageSeeder::class);
});

test('guest cannot access blog categories', function () {
    $this->get(route('admin.blog-categories.index'))->assertRedirect(route('login'));
});

test('regular user cannot access blog categories', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.blog-categories.index'))
        ->assertForbidden();
});

test('admin can view blog categories page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.blog-categories.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/Blog/Categories/Index')
            ->has('categories')
        );
});

test('admin can create blog category with translated slugs', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $kz = Language::query()->where('code', 'kz')->firstOrFail();

    $response = $this->actingAs($admin)->post(route('admin.blog-categories.store'), [
        'icon' => 'Newspaper',
        'is_active' => true,
        'sort_order' => 1,
        'translations' => [
            $ru->id => [
                'language_id' => $ru->id,
                'name' => 'Новости компании',
            ],
            $kz->id => [
                'language_id' => $kz->id,
                'name' => 'Компания жаңалықтары',
            ],
        ],
    ]);

    $response->assertRedirect(route('admin.blog-categories.index'));

    $category = BlogCategory::query()->first();
    expect($category)->not->toBeNull();
    expect($category->icon)->toBe('Newspaper');

    $ruTranslation = BlogCategoryTranslation::query()
        ->where('blog_category_id', $category->id)
        ->where('language_id', $ru->id)
        ->first();

    $kzTranslation = BlogCategoryTranslation::query()
        ->where('blog_category_id', $category->id)
        ->where('language_id', $kz->id)
        ->first();

    expect($ruTranslation->slug)->toBe('novosti-kompanii');
    expect($kzTranslation->slug)->not->toBe('');
});

test('admin can delete blog category', function () {
    $admin = User::factory()->admin()->create();
    $category = BlogCategory::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.blog-categories.destroy', $category))
        ->assertRedirect(route('admin.blog-categories.index'));

    $this->assertDatabaseMissing('blog_categories', ['id' => $category->id]);
});
