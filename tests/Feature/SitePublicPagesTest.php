<?php

use App\Enums\BlogPostStatus;
use App\Enums\ProjectStatus;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\Language;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectCategoryTranslation;
use App\Models\ProjectTranslation;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\BlogSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\SiteSettingsSeeder;

beforeEach(function () {
    $this->seed(LanguageSeeder::class);
    $this->seed(SiteSettingsSeeder::class);
    $this->seed(AdminUserSeeder::class);
});

test('home page renders public site component', function () {
    $this->seed(BlogSeeder::class);
    $this->seed(ProjectSeeder::class);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('site/Home')
            ->has('site')
            ->where('site.name', 'TITS')
            ->has('posts', 6)
            ->has('projects', 3)
        );
});

test('home page shows up to ten published projects on slider', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $category = ProjectCategory::factory()->create();
    ProjectCategoryTranslation::factory()->create([
        'project_category_id' => $category->id,
        'language_id' => $ru->id,
        'name' => 'Категория',
        'slug' => 'kategoriya',
    ]);

    for ($i = 1; $i <= 6; $i++) {
        $project = Project::factory()->published()->create([
            'project_category_id' => $category->id,
            'user_id' => $admin->id,
        ]);

        ProjectTranslation::factory()->create([
            'project_id' => $project->id,
            'language_id' => $ru->id,
            'title' => "Проект {$i}",
            'slug' => "proekt-{$i}",
            'content' => '<p>Описание</p>',
        ]);
    }

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('site/Home')
            ->has('projects', 6)
        );
});

test('draft projects are hidden on public site while published ones are visible', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $category = ProjectCategory::factory()->create();
    ProjectCategoryTranslation::factory()->create([
        'project_category_id' => $category->id,
        'language_id' => $ru->id,
        'name' => 'Категория',
        'slug' => 'kategoriya',
    ]);

    for ($i = 1; $i <= 3; $i++) {
        $project = Project::factory()->published()->create([
            'project_category_id' => $category->id,
            'user_id' => $admin->id,
        ]);

        ProjectTranslation::factory()->create([
            'project_id' => $project->id,
            'language_id' => $ru->id,
            'title' => "Опубликован {$i}",
            'slug' => "published-{$i}",
            'content' => '<p>Описание</p>',
        ]);
    }

    for ($i = 1; $i <= 3; $i++) {
        $project = Project::factory()->create([
            'project_category_id' => $category->id,
            'user_id' => $admin->id,
            'status' => ProjectStatus::Draft,
        ]);

        ProjectTranslation::factory()->create([
            'project_id' => $project->id,
            'language_id' => $ru->id,
            'title' => "Черновик {$i}",
            'slug' => "draft-{$i}",
            'content' => '<p>Скрыто</p>',
        ]);
    }

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('projects', 3));

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('projects.data', 3)
            ->where('projects.total', 3)
        );
});

test('projects with only russian translation appear on kazakh site', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $kz = Language::query()->where('code', 'kz')->firstOrFail();
    $category = ProjectCategory::factory()->create();
    ProjectCategoryTranslation::factory()->create([
        'project_category_id' => $category->id,
        'language_id' => $ru->id,
        'name' => 'Категория',
        'slug' => 'kategoriya',
    ]);
    ProjectCategoryTranslation::factory()->create([
        'project_category_id' => $category->id,
        'language_id' => $kz->id,
        'name' => 'Санат',
        'slug' => 'sanat',
    ]);

    for ($i = 1; $i <= 3; $i++) {
        $project = Project::factory()->published()->create([
            'project_category_id' => $category->id,
            'user_id' => $admin->id,
        ]);

        ProjectTranslation::factory()->create([
            'project_id' => $project->id,
            'language_id' => $ru->id,
            'title' => "RU only {$i}",
            'slug' => "ru-only-{$i}",
            'content' => '<p>RU</p>',
        ]);
    }

    for ($i = 1; $i <= 3; $i++) {
        $project = Project::factory()->published()->create([
            'project_category_id' => $category->id,
            'user_id' => $admin->id,
        ]);

        ProjectTranslation::factory()->create([
            'project_id' => $project->id,
            'language_id' => $ru->id,
            'title' => "Both {$i}",
            'slug' => "both-ru-{$i}",
            'content' => '<p>RU</p>',
        ]);

        ProjectTranslation::factory()->create([
            'project_id' => $project->id,
            'language_id' => $kz->id,
            'title' => "Екеуі {$i}",
            'slug' => "both-kz-{$i}",
            'content' => '<p>KZ</p>',
        ]);
    }

    $this->withSession(['locale' => 'kz'])
        ->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('projects.data', 6)
            ->where('projects.total', 6)
        );
});

test('unknown route returns site not found page', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertInertia(fn ($page) => $page
            ->component('site/NotFound')
            ->has('site')
            ->where('site.name', 'TITS')
            ->has('locale')
            ->has('i18n')
        );
});

test('maintenance page can be viewed', function () {
    $this->get(route('maintenance'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('site/Maintenance')
            ->has('site')
        );
});

test('guest sees maintenance page when maintenance mode is enabled', function () {
    SiteSetting::instance()->update(['maintenance_mode' => true]);

    $this->get(route('home'))
        ->assertStatus(503)
        ->assertInertia(fn ($page) => $page
            ->component('site/Maintenance')
            ->has('site')
        );
});

test('admin can browse public site during maintenance mode', function () {
    $admin = User::factory()->admin()->create();
    SiteSetting::instance()->update(['maintenance_mode' => true]);

    $this->actingAs($admin)
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('site/Home'));
});

test('unknown route shows maintenance page during maintenance mode', function () {
    SiteSetting::instance()->update(['maintenance_mode' => true]);

    $this->get('/this-page-does-not-exist')
        ->assertStatus(503)
        ->assertInertia(fn ($page) => $page->component('site/Maintenance'));
});

test('home page uses site root view', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    expect($response->getContent())->toContain('resources/js/site.ts');
});

test('blog index page renders published posts', function () {
    $this->seed(BlogSeeder::class);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('site/Blog/Index')
            ->has('posts.data', 6)
            ->where('posts.per_page', 12)
            ->has('categories', 6)
            ->where('posts.data.0.title', 'Когда стоит нести ноутбук в сервис, а когда хватит чистки')
        );
});

test('blog show page renders post by slug', function () {
    $this->seed(BlogSeeder::class);

    $this->get(route('blog.show', ['slug' => 'kogda-nesti-noutbuk-v-servis']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('site/Blog/Show')
            ->where('post.slug', 'kogda-nesti-noutbuk-v-servis')
            ->where('post.category.name', 'Ремонт')
        );
});

test('projects index page renders published projects', function () {
    $this->seed(ProjectSeeder::class);

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('site/Projects/Index')
            ->has('projects.data', 3)
            ->where('projects.per_page', 10)
            ->has('categories', 3)
            ->where('projects.data.0.title', 'Приложение для сервисного центра')
        );
});

test('project show page renders project by slug', function () {
    $this->seed(ProjectSeeder::class);

    $this->get(route('projects.show', ['slug' => 'prilozhenie-dlya-servisnogo-centra']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('site/Projects/Show')
            ->where('project.slug', 'prilozhenie-dlya-servisnogo-centra')
            ->where('project.category.name', 'Мобильная разработка')
        );
});

test('draft blog post is not shown on public site', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $category = BlogCategory::factory()->create();
    BlogCategoryTranslation::factory()->create([
        'blog_category_id' => $category->id,
        'language_id' => $ru->id,
        'name' => 'Тест',
        'slug' => 'test',
    ]);

    $post = BlogPost::factory()->create([
        'user_id' => $admin->id,
        'blog_category_id' => $category->id,
        'status' => BlogPostStatus::Draft,
    ]);

    BlogPostTranslation::factory()->create([
        'blog_post_id' => $post->id,
        'language_id' => $ru->id,
        'title' => 'Черновик',
        'slug' => 'draft-post',
        'content' => '<p>Скрыто</p>',
    ]);

    $this->get(route('blog.show', ['slug' => 'draft-post']))->assertNotFound();
});

test('projects index paginates published projects', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $category = ProjectCategory::factory()->create();
    ProjectCategoryTranslation::factory()->create([
        'project_category_id' => $category->id,
        'language_id' => $ru->id,
        'name' => 'Категория',
        'slug' => 'kategoriya',
    ]);

    for ($i = 1; $i <= 11; $i++) {
        $project = Project::factory()->published()->create([
            'project_category_id' => $category->id,
            'user_id' => $admin->id,
        ]);

        ProjectTranslation::factory()->create([
            'project_id' => $project->id,
            'language_id' => $ru->id,
            'title' => "Проект {$i}",
            'slug' => "proekt-{$i}",
            'content' => '<p>Описание</p>',
        ]);
    }

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('projects.data', 10)
            ->where('projects.total', 11)
            ->where('projects.per_page', 10)
        );

    $this->get(route('projects.index', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('projects.data', 1)
            ->where('projects.current_page', 2)
        );
});

test('blog index paginates published posts', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $category = BlogCategory::factory()->create();
    BlogCategoryTranslation::factory()->create([
        'blog_category_id' => $category->id,
        'language_id' => $ru->id,
        'name' => 'Категория',
        'slug' => 'kategoriya',
    ]);

    for ($i = 1; $i <= 13; $i++) {
        $post = BlogPost::factory()->published()->create([
            'blog_category_id' => $category->id,
            'user_id' => $admin->id,
        ]);

        BlogPostTranslation::factory()->create([
            'blog_post_id' => $post->id,
            'language_id' => $ru->id,
            'title' => "Пост {$i}",
            'slug' => "post-{$i}",
            'content' => '<p>Текст</p>',
        ]);
    }

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('posts.data', 12)
            ->where('posts.total', 13)
            ->where('posts.per_page', 12)
        );

    $this->get(route('blog.index', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('posts.data', 1)
            ->where('posts.current_page', 2)
        );
});

test('public site shares locale and languages', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('locale', 'ru')
            ->has('languages', 2)
            ->has('i18n.site.nav.blog')
        );
});
