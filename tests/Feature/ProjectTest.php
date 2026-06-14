<?php

use App\Enums\ProjectStatus;
use App\Models\Language;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectCategoryTranslation;
use App\Models\ProjectTranslation;
use App\Models\User;
use Carbon\Carbon;
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

test('admin can update project', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $kz = Language::query()->where('code', 'kz')->firstOrFail();

    $project = Project::factory()->create([
        'user_id' => $admin->id,
        'status' => ProjectStatus::Draft,
    ]);

    ProjectTranslation::factory()->create([
        'project_id' => $project->id,
        'language_id' => $ru->id,
        'title' => 'Старый проект',
        'slug' => 'staryi-proekt',
        'content' => '<p>Старое описание</p>',
    ]);

    ProjectTranslation::factory()->create([
        'project_id' => $project->id,
        'language_id' => $kz->id,
        'title' => 'Ескі жоба',
        'slug' => 'eski-zhoba',
        'content' => '<p>Ескі сипаттама</p>',
    ]);

    $this->actingAs($admin)->post(route('admin.projects.update', $project), [
        'status' => ProjectStatus::Published->value,
        'published_at' => '2026-06-13T12:00',
        'translations' => [
            $ru->id => [
                'language_id' => $ru->id,
                'title' => 'Обновлённый проект',
                'content' => '<p>Новое описание</p>',
            ],
            $kz->id => [
                'language_id' => $kz->id,
                'title' => 'Жаңартылған жоба',
                'content' => '<p>Жаңа сипаттама</p>',
            ],
        ],
    ])->assertRedirect(route('admin.projects.index'));

    $project->refresh();

    expect($project->status)->toBe(ProjectStatus::Published)
        ->and($project->translations()->where('language_id', $ru->id)->value('title'))->toBe('Обновлённый проект');
});

test('published project is visible on site regardless of display date', function () {
    Carbon::setTestNow('2026-06-14 10:00:00');

    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();

    $project = Project::factory()->published()->create([
        'user_id' => $admin->id,
        'published_at' => '2026-12-31 23:59:00',
    ]);

    ProjectTranslation::factory()->create([
        'project_id' => $project->id,
        'language_id' => $ru->id,
        'title' => 'Проект с датой в будущем',
        'slug' => 'proekt-s-datoj-v-budushchem',
        'content' => '<p>Описание</p>',
    ]);

    expect($project->siteVisibilityStatus())->toBe('visible');

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('projects.data', 1)
            ->where('projects.data.0.title', 'Проект с датой в будущем')
        );

    Carbon::setTestNow();
});
