<?php

use App\Models\Language;
use App\Models\ProjectCategory;
use App\Models\ProjectCategoryTranslation;
use App\Models\User;
use Database\Seeders\LanguageSeeder;

beforeEach(function () {
    $this->seed(LanguageSeeder::class);
});

test('guest cannot access project categories', function () {
    $this->get(route('admin.project-categories.index'))->assertRedirect(route('login'));
});

test('admin can create project category with translated slugs', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $kz = Language::query()->where('code', 'kz')->firstOrFail();

    $this->actingAs($admin)->post(route('admin.project-categories.store'), [
        'icon' => 'Briefcase',
        'is_active' => true,
        'sort_order' => 1,
        'translations' => [
            $ru->id => [
                'language_id' => $ru->id,
                'name' => 'Веб-разработка',
            ],
            $kz->id => [
                'language_id' => $kz->id,
                'name' => 'Веб-әзірлеу',
            ],
        ],
    ])->assertRedirect(route('admin.project-categories.index'));

    $category = ProjectCategory::query()->first();
    expect($category)->not->toBeNull();

    $ruTranslation = ProjectCategoryTranslation::query()
        ->where('project_category_id', $category->id)
        ->where('language_id', $ru->id)
        ->first();

    expect($ruTranslation->slug)->toBe('veb-razrabotka');
});
