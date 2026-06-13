<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingsRequest;
use App\Models\Language;
use App\Models\SiteSetting;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingsController extends Controller
{
    public function edit(): Response
    {
        $this->authorize('viewAny', SiteSetting::class);

        $setting = SiteSetting::instance()->load('translations.language');
        $languages = Language::query()->orderBy('name')->get(['id', 'name', 'code']);

        $translations = $languages->mapWithKeys(function (Language $language) use ($setting) {
            $translation = $setting->translations->firstWhere('language_id', $language->id);

            return [
                $language->id => [
                    'language_id' => $language->id,
                    'code' => $language->code,
                    'name' => $language->name,
                    'site_name' => $translation?->site_name ?? '',
                    'description' => $translation?->description ?? '',
                    'keywords' => $translation?->keywords ?? '',
                    'home_title' => $translation?->home_title ?? '',
                    'home_meta_description' => $translation?->home_meta_description ?? '',
                    'blog_index_title' => $translation?->blog_index_title ?? '',
                    'blog_index_meta_description' => $translation?->blog_index_meta_description ?? '',
                    'projects_index_title' => $translation?->projects_index_title ?? '',
                    'projects_index_meta_description' => $translation?->projects_index_meta_description ?? '',
                ],
            ];
        });

        return Inertia::render('admin/SiteSettings', [
            'setting' => [
                'maintenance_mode' => $setting->maintenance_mode,
                'sitemap_enabled' => (bool) ($setting->sitemap_enabled ?? true),
                'title_separator' => $setting->title_separator ?? ' - ',
                'default_robots' => $setting->default_robots ?? 'index, follow',
                'twitter_handle' => $setting->twitter_handle,
                'google_site_verification' => $setting->google_site_verification,
                'yandex_verification' => $setting->yandex_verification,
                'robots_txt' => $setting->robots_txt,
                'logo_url' => $setting->logo_path ? Storage::disk('public')->url($setting->logo_path) : null,
                'favicon_url' => $setting->favicon_path ? Storage::disk('public')->url($setting->favicon_path) : null,
                'og_image_url' => $setting->og_image_path ? Storage::disk('public')->url($setting->og_image_path) : null,
                'phone' => $setting->phone,
                'address' => $setting->address,
                'social' => array_merge([
                    'facebook' => null,
                    'instagram' => null,
                    'telegram' => null,
                    'whatsapp' => null,
                    'youtube' => null,
                    'tiktok' => null,
                ], $setting->social_links ?? []),
            ],
            'seoByLanguage' => $translations,
            'languages' => $languages,
        ]);
    }

    public function update(UpdateSiteSettingsRequest $request, SiteSettingsService $siteSettingsService): RedirectResponse
    {
        $setting = SiteSetting::instance();
        $validated = $request->validated();

        if ($request->boolean('remove_logo') && $setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
            $setting->logo_path = null;
        }

        if ($request->boolean('remove_favicon') && $setting->favicon_path) {
            Storage::disk('public')->delete($setting->favicon_path);
            $setting->favicon_path = null;
        }

        if ($request->boolean('remove_og_image') && $setting->og_image_path) {
            Storage::disk('public')->delete($setting->og_image_path);
            $setting->og_image_path = null;
        }

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            $setting->logo_path = $request->file('logo')->store('site', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon_path) {
                Storage::disk('public')->delete($setting->favicon_path);
            }

            $setting->favicon_path = $request->file('favicon')->store('site', 'public');
        }

        if ($request->hasFile('og_image')) {
            if ($setting->og_image_path) {
                Storage::disk('public')->delete($setting->og_image_path);
            }

            $setting->og_image_path = $request->file('og_image')->store('site', 'public');
        }

        $setting->fill([
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'social_links' => $validated['social'] ?? [],
            'maintenance_mode' => $request->boolean('maintenance_mode'),
            'sitemap_enabled' => $request->boolean('sitemap_enabled'),
            'title_separator' => $validated['title_separator'],
            'default_robots' => $validated['default_robots'],
            'twitter_handle' => $validated['twitter_handle'] ?? null,
            'google_site_verification' => $validated['google_site_verification'] ?? null,
            'yandex_verification' => $validated['yandex_verification'] ?? null,
            'robots_txt' => $validated['robots_txt'] ?? null,
        ]);
        $setting->save();

        foreach ($validated['translations'] as $translationData) {
            $setting->translations()->updateOrCreate(
                ['language_id' => $translationData['language_id']],
                [
                    'site_name' => $translationData['site_name'],
                    'description' => $translationData['description'] ?? null,
                    'keywords' => $translationData['keywords'] ?? null,
                    'home_title' => $translationData['home_title'] ?? null,
                    'home_meta_description' => $translationData['home_meta_description'] ?? null,
                    'blog_index_title' => $translationData['blog_index_title'] ?? null,
                    'blog_index_meta_description' => $translationData['blog_index_meta_description'] ?? null,
                    'projects_index_title' => $translationData['projects_index_title'] ?? null,
                    'projects_index_meta_description' => $translationData['projects_index_meta_description'] ?? null,
                ],
            );
        }

        $siteSettingsService->forget();

        return back()->with('status', 'site-settings-saved');
    }
}
