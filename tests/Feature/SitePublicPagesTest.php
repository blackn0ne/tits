<?php

use App\Enums\BlogPostStatus;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\Language;
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
            ->has('posts', 6)
            ->has('categories', 6)
            ->where('posts.0.title', 'Когда стоит нести ноутбук в сервис, а когда хватит чистки')
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
            ->has('projects', 3)
            ->has('categories', 3)
            ->where('projects.0.title', 'Приложение для сервисного центра')
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

test('public site shares locale and languages', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('locale', 'ru')
            ->has('languages', 2)
            ->has('i18n.site.nav.blog')
        );
});
