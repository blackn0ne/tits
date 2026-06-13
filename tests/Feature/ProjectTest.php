<?php

use App\Enums\ProjectStatus;
use App\Models\Language;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectCategoryTranslation;
use App\Models\ProjectTranslation;
use App\Models\User;
use Database\Seeders\LanguageSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(LanguageSeeder::class);
    Storage::fake('public');
});

test('guest cannot access projects', function () {
    $this->get(route('admin.projects.index'))->assertRedirect(route('login'));
});

test('admin can create project with kazakh slug', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $kz = Language::query()->where('code', 'kz')->firstOrFail();

    $category = ProjectCategory::factory()->create();
    ProjectCategoryTranslation::factory()->create([
        'project_category_id' => $category->id,
        'language_id' => $ru->id,
        'name' => 'Категория',
        'slug' => 'kategoriia',
    ]);

    $this->actingAs($admin)->post(route('admin.projects.store'), [
        'project_category_id' => $category->id,
        'status' => ProjectStatus::Published->value,
        'published_at' => '2026-06-13T10:00',
        'banner' => UploadedFile::fake()->image('banner.jpg'),
        'translations' => [
            $ru->id => [
                'language_id' => $ru->id,
                'title' => 'Корпоративный сайт',
                'content' => '<p>Описание</p>',
            ],
            $kz->id => [
                'language_id' => $kz->id,
                'title' => 'Корпоративтік сайт',
                'content' => '<p>Сипаттама</p>',
            ],
        ],
    ])->assertRedirect(route('admin.projects.index'));

    $project = Project::query()->first();
    expect($project)->not->toBeNull();

    $kzTranslation = ProjectTranslation::query()
        ->where('project_id', $project->id)
        ->where('language_id', $kz->id)
        ->first();

    expect($kzTranslation->slug)->not->toBe('');
});
