<?php

use App\Models\Language;
use App\Services\TranslationLoader;
use Database\Seeders\LanguageSeeder;

beforeEach(function () {
    $this->seed(LanguageSeeder::class);
});

test('default locale is russian', function () {
    expect(config('app.locale'))->toBe('ru')
        ->and(config('app.fallback_locale'))->toBe('ru');
});

test('languages table is seeded with ru and kz', function () {
    expect(Language::query()->pluck('code')->all())
        ->toContain('ru', 'kz')
        ->toHaveCount(2);
});

test('user can switch locale to kazakh', function () {
    $response = $this->from('/login')->post('/locale/kz');

    $response->assertRedirect('/login');
    expect(session('locale'))->toBe('kz');
});

test('user can switch locale to kazakh via get', function () {
    $response = $this->from('/')->get('/locale/kz');

    $response->assertRedirect('/');
    expect(session('locale'))->toBe('kz');
});

test('invalid locale is rejected', function () {
    $this->post('/locale/en')->assertStatus(400);
});

test('login page shares locale translations and languages', function () {
    $response = $this->get('/login');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('locale', 'ru')
            ->has('i18n.auth.login.title')
            ->has('languages', 2)
        );
});

test('kazakh translations are loaded from lang/kz.json', function () {
    $response = $this->withSession(['locale' => 'kz'])->get('/login');

    $response->assertInertia(fn ($page) => $page
        ->where('locale', 'kz')
        ->where('i18n.auth.login.title', 'Аккаунтқа кіру')
        ->where('i18n.site.hero.tagline', 'Техниканы жөндейміз, құжаттарды басып шығарамыз және цифрлық өнімдер жасаймыз — бәрі бір жерде.')
    );
});

test('translation loader reads lang/ru.json', function () {
    $loader = app(TranslationLoader::class);

    expect($loader->has('ru'))->toBeTrue()
        ->and($loader->get('ru', 'auth.login.title'))->toBe('Вход в аккаунт');
});

test('user role labels are translated by locale', function () {
    app()->setLocale('ru');
    expect(\App\Enums\UserRole::Admin->label())->toBe('Администратор')
        ->and(\App\Enums\UserRole::User->label())->toBe('Пользователь');

    app()->setLocale('kz');
    expect(\App\Enums\UserRole::Admin->label())->toBe('Әкімші')
        ->and(\App\Enums\UserRole::User->label())->toBe('Пайдаланушы');
});
