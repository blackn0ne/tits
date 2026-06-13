<?php

use App\Models\Language;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\SiteSettingsSeeder;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->seed(LanguageSeeder::class);
    $this->seed(SiteSettingsSeeder::class);
});

test('guest cannot access site settings', function () {
    $this->get(route('admin.site-settings.edit'))->assertRedirect(route('login'));
});

test('guest is redirected to login from admin root', function () {
    $this->get('/admin')->assertRedirect(route('login'));
});

test('admin is redirected from admin root to site settings', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertRedirect(route('admin.site-settings.edit'));
});

test('regular user cannot access site settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.site-settings.edit'))
        ->assertForbidden();
});

test('admin can view site settings page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.site-settings.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/SiteSettings')
            ->has('setting')
            ->has('seoByLanguage')
            ->where('i18n.admin.site.title', 'Настройки сайта')
            ->where('i18n.nav.platform', 'Меню')
        );
});

test('admin can update site settings', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $kz = Language::query()->where('code', 'kz')->firstOrFail();

    $response = $this->actingAs($admin)->post(route('admin.site-settings.update'), [
        'maintenance_mode' => true,
        'sitemap_enabled' => true,
        'title_separator' => ' - ',
        'default_robots' => 'index, follow',
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
                'site_name' => 'TITS Updated',
                'description' => 'Новое описание',
                'keywords' => 'tits, test',
            ],
            $kz->id => [
                'language_id' => $kz->id,
                'site_name' => 'TITS KZ',
                'description' => 'Жаңа сипаттама',
                'keywords' => 'tits, test',
            ],
        ],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('site_settings', [
        'phone' => '+7 777 777 77 77',
        'address' => 'Алматы',
        'maintenance_mode' => true,
    ]);
});

test('admin can disable maintenance mode from site settings', function () {
    $admin = User::factory()->admin()->create();
    $ru = Language::query()->where('code', 'ru')->firstOrFail();
    $kz = Language::query()->where('code', 'kz')->firstOrFail();

    SiteSetting::instance()->update(['maintenance_mode' => true]);

    $this->actingAs($admin)->post(route('admin.site-settings.update'), [
        'maintenance_mode' => false,
        'sitemap_enabled' => true,
        'title_separator' => ' - ',
        'default_robots' => 'index, follow',
        'phone' => '+7 700 000-00-00',
        'address' => 'Казахстан',
        'social' => [
            'facebook' => null,
            'instagram' => null,
            'telegram' => null,
            'whatsapp' => null,
            'youtube' => null,
            'tiktok' => null,
        ],
        'translations' => [
            $ru->id => [
                'language_id' => $ru->id,
                'site_name' => 'TITS',
                'description' => 'Описание',
                'keywords' => 'tits',
            ],
            $kz->id => [
                'language_id' => $kz->id,
                'site_name' => 'TITS',
                'description' => 'Сипаттама',
                'keywords' => 'tits',
            ],
        ],
    ])->assertRedirect();

    $this->assertDatabaseHas('site_settings', [
        'maintenance_mode' => false,
    ]);
});

test('login page shares site seo data', function () {
    $response = $this->get('/login');

    $response->assertInertia(fn ($page) => $page
        ->has('site.name')
        ->has('site.description')
        ->has('site.keywords')
    );
});

test('cached site settings without seo fields use defaults', function () {
    Cache::put('site_settings.v2.ru', [
        'name' => 'TITS',
        'description' => 'Описание',
        'keywords' => 'tits',
        'logo_url' => null,
        'favicon_url' => null,
        'phone' => null,
        'address' => null,
        'social' => [
            'facebook' => null,
            'instagram' => null,
            'telegram' => null,
            'whatsapp' => null,
            'youtube' => null,
            'tiktok' => null,
        ],
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('site.title_separator', ' - ')
            ->where('site.default_robots', 'index, follow')
            ->where('site.sitemap_enabled', true)
            ->has('seo.title')
        );
});
