<?php

namespace App\Services;

use App\Data\SiteSettingsData;
use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SiteSettingsService
{
    private const CACHE_KEY = 'site_settings.v2';

    public function forLocale(?string $locale = null): SiteSettingsData
    {
        $locale = $locale ?? app()->getLocale();

        /** @var array<string, mixed> $data */
        $data = Cache::remember(
            self::CACHE_KEY.'.'.$locale,
            now()->addHour(),
            fn (): array => $this->resolve($locale)->toArray(),
        );

        return $this->fromArray($data);
    }

    public function forget(): void
    {
        Language::query()->pluck('code')->each(
            fn (string $code) => Cache::forget(self::CACHE_KEY.'.'.$code),
        );

        Cache::forget(self::CACHE_KEY.'.maintenance');
    }

    public function isMaintenanceMode(): bool
    {
        return Cache::remember(
            self::CACHE_KEY.'.maintenance',
            now()->addHour(),
            fn (): bool => (bool) SiteSetting::query()->value('maintenance_mode'),
        );
    }

    public function shouldBypassMaintenance(Request $request): bool
    {
        if ($request->is('admin', 'admin/*', 'login', 'logout', 'register', 'maintenance', 'up', 'robots.txt', 'sitemap.xml')) {
            return true;
        }

        if ($request->is(
            'forgot-password',
            'reset-password*',
            'verify-email',
            'email/verification-notification',
            'confirm-password',
            'locale',
            'locale/*',
        )) {
            return true;
        }

        if ($request->user()?->isAdmin() === true) {
            return true;
        }

        return false;
    }

    private function resolve(string $locale): SiteSettingsData
    {
        $setting = SiteSetting::query()
            ->with(['translations.language'])
            ->first();

        if ($setting === null) {
            return $this->fallback();
        }

        $translation = $setting->translations
            ->first(fn ($item) => $item->language?->code === $locale)
            ?? $setting->translations->first();

        $social = array_merge($this->emptySocial(), $setting->social_links ?? []);

        return new SiteSettingsData(
            name: $translation?->site_name ?? (string) config('app.name'),
            description: $translation?->description,
            keywords: $translation?->keywords,
            logoUrl: $this->assetUrl($setting->logo_path),
            faviconUrl: $this->assetUrl($setting->favicon_path),
            phone: $setting->phone,
            address: $setting->address,
            social: $social,
            titleSeparator: filled($setting->title_separator) ? (string) $setting->title_separator : ' - ',
            defaultRobots: filled($setting->default_robots) ? (string) $setting->default_robots : 'index, follow',
            ogImageUrl: $this->assetUrl($setting->og_image_path) ?? $this->assetUrl($setting->logo_path),
            twitterHandle: $setting->twitter_handle,
            googleSiteVerification: $setting->google_site_verification,
            yandexVerification: $setting->yandex_verification,
            sitemapEnabled: (bool) ($setting->sitemap_enabled ?? true),
            robotsTxt: $setting->robots_txt,
            homeTitle: $translation?->home_title,
            homeMetaDescription: $translation?->home_meta_description,
            blogIndexTitle: $translation?->blog_index_title,
            blogIndexMetaDescription: $translation?->blog_index_meta_description,
            projectsIndexTitle: $translation?->projects_index_title,
            projectsIndexMetaDescription: $translation?->projects_index_meta_description,
        );
    }

    private function fallback(): SiteSettingsData
    {
        return $this->fromArray($this->fallbackArray());
    }

    /**
     * @return array<string, mixed>
     */
    private function fallbackArray(): array
    {
        return [
            'name' => (string) config('app.name'),
            'description' => null,
            'keywords' => null,
            'logo_url' => null,
            'favicon_url' => null,
            'phone' => null,
            'address' => null,
            'social' => $this->emptySocial(),
            'title_separator' => ' - ',
            'default_robots' => 'index, follow',
            'og_image_url' => null,
            'twitter_handle' => null,
            'google_site_verification' => null,
            'yandex_verification' => null,
            'sitemap_enabled' => true,
            'robots_txt' => null,
            'home_title' => null,
            'home_meta_description' => null,
            'blog_index_title' => null,
            'blog_index_meta_description' => null,
            'projects_index_title' => null,
            'projects_index_meta_description' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function fromArray(array $data): SiteSettingsData
    {
        return new SiteSettingsData(
            name: (string) ($data['name'] ?? config('app.name')),
            description: isset($data['description']) ? (string) $data['description'] : null,
            keywords: isset($data['keywords']) ? (string) $data['keywords'] : null,
            logoUrl: isset($data['logo_url']) ? (string) $data['logo_url'] : null,
            faviconUrl: isset($data['favicon_url']) ? (string) $data['favicon_url'] : null,
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            address: isset($data['address']) ? (string) $data['address'] : null,
            social: array_merge($this->emptySocial(), is_array($data['social'] ?? null) ? $data['social'] : []),
            titleSeparator: filled($data['title_separator'] ?? null) ? (string) $data['title_separator'] : ' - ',
            defaultRobots: filled($data['default_robots'] ?? null) ? (string) $data['default_robots'] : 'index, follow',
            ogImageUrl: isset($data['og_image_url']) ? (string) $data['og_image_url'] : null,
            twitterHandle: isset($data['twitter_handle']) ? (string) $data['twitter_handle'] : null,
            googleSiteVerification: isset($data['google_site_verification']) ? (string) $data['google_site_verification'] : null,
            yandexVerification: isset($data['yandex_verification']) ? (string) $data['yandex_verification'] : null,
            sitemapEnabled: (bool) ($data['sitemap_enabled'] ?? true),
            robotsTxt: isset($data['robots_txt']) ? (string) $data['robots_txt'] : null,
            homeTitle: isset($data['home_title']) ? (string) $data['home_title'] : null,
            homeMetaDescription: isset($data['home_meta_description']) ? (string) $data['home_meta_description'] : null,
            blogIndexTitle: isset($data['blog_index_title']) ? (string) $data['blog_index_title'] : null,
            blogIndexMetaDescription: isset($data['blog_index_meta_description']) ? (string) $data['blog_index_meta_description'] : null,
            projectsIndexTitle: isset($data['projects_index_title']) ? (string) $data['projects_index_title'] : null,
            projectsIndexMetaDescription: isset($data['projects_index_meta_description']) ? (string) $data['projects_index_meta_description'] : null,
        );
    }

    /**
     * @return array<string, string|null>
     */
    private function emptySocial(): array
    {
        return [
            'facebook' => null,
            'instagram' => null,
            'telegram' => null,
            'whatsapp' => null,
            'youtube' => null,
            'tiktok' => null,
        ];
    }

    private function assetUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
