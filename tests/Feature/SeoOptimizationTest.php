<?php

use App\Models\Language;
use App\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\SiteSettingsSeeder;

beforeEach(function () {
    $this->seed(LanguageSeeder::class);
    $this->seed(SiteSettingsSeeder::class);
});

test('home page includes resolved seo props', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('site/Home')
            ->has('seo.title')
            ->has('seo.description')
            ->has('seo.canonical')
            ->has('seo.robots')
        );
});

test('robots.txt is accessible', function () {
    $this->get(route('seo.robots'))
        ->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8');
});

test('sitemap.xml is accessible when enabled', function () {
    $this->get(route('seo.sitemap'))
        ->assertOk()
        ->assertHeader('content-type', 'application/xml; charset=UTF-8');
});

test('admin can update seo settings', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $kz = Language::query()->where('code', 'kz')->firstOrFail();

    $this->actingAs($admin)->post(route('admin.site-settings.update'), [
        'maintenance_mode' => false,
        'sitemap_enabled' => true,
        'title_separator' => ' | ',
        'default_robots' => 'index, follow',
        'twitter_handle' => '@tits',
        'google_site_verification' => 'google-token',
        'yandex_verification' => 'yandex-token',
        'robots_txt' => "User-agent: *\nDisallow: /admin\n",
        'phone' => '+7 777 777 77 77',
        'address' => 'Алматы',
        'social' => [
            'facebook' => null,
            'instagram' => 'https://instagram.com/tits',
            'telegram' => null,
            'whatsapp' => null,
            'youtube' => null,
            'tiktok' => null,
        ],
        'translations' => [
            $ru->id => [
                'language_id' => $ru->id,
                'site_name' => 'TITS SEO',
                'description' => 'Глобальное описание',
                'keywords' => 'tits, seo',
                'home_title' => 'TITS — IT в Туркестане',
                'home_meta_description' => 'Описание главной',
                'blog_index_title' => 'Блог TITS',
                'blog_index_meta_description' => 'Статьи компании',
                'projects_index_title' => 'Наши работы',
                'projects_index_meta_description' => 'Портфолио проектов',
            ],
            $kz->id => [
                'language_id' => $kz->id,
                'site_name' => 'TITS KZ',
                'description' => 'Жалпы сипаттама',
                'keywords' => 'tits, seo',
                'home_title' => '',
                'home_meta_description' => '',
                'blog_index_title' => '',
                'blog_index_meta_description' => '',
                'projects_index_title' => '',
                'projects_index_meta_description' => '',
            ],
        ],
    ])->assertRedirect();

    $this->get(route('home'))
        ->assertInertia(fn ($page) => $page
            ->where('seo.title', 'TITS — IT в Туркестане')
            ->where('site.twitter_handle', '@tits')
            ->where('site.sitemap_enabled', true)
        );
});
