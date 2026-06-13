<?php

namespace App\Services;

use App\Data\SeoData;
use App\Data\SiteSettingsData;

class SeoService
{
    public function __construct(
        private SiteSettingsService $siteSettingsService,
        private TranslationLoader $translationLoader,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function forHome(?string $locale = null): array
    {
        $site = $this->siteSettingsService->forLocale($locale);
        $locale = $locale ?? app()->getLocale();

        return $this->build(
            site: $site,
            pageTitle: $site->homeTitle ?? $this->t($locale, 'site.home_title'),
            description: $site->homeMetaDescription ?? $site->description,
            canonical: route('home'),
            standaloneTitle: $site->homeTitle !== null && $site->homeTitle !== '',
        )->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function forBlogIndex(?string $locale = null): array
    {
        $site = $this->siteSettingsService->forLocale($locale);
        $locale = $locale ?? app()->getLocale();

        return $this->build(
            site: $site,
            pageTitle: $site->blogIndexTitle ?? $this->t($locale, 'site.blog.index_title'),
            description: $site->blogIndexMetaDescription ?? $site->description,
            canonical: route('blog.index'),
        )->toArray();
    }

    /**
     * @param  array<string, mixed>  $post
     * @return array<string, mixed>
     */
    public function forBlogShow(array $post, ?string $locale = null): array
    {
        $site = $this->siteSettingsService->forLocale($locale);

        return $this->build(
            site: $site,
            pageTitle: $post['meta_title'] ?? $post['title'],
            description: $post['meta_description'] ?? $post['excerpt'] ?? $site->description,
            image: $post['banner_url'] ?? null,
            canonical: route('blog.show', $post['slug']),
            ogType: 'article',
        )->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function forProjectsIndex(?string $locale = null): array
    {
        $site = $this->siteSettingsService->forLocale($locale);
        $locale = $locale ?? app()->getLocale();

        return $this->build(
            site: $site,
            pageTitle: $site->projectsIndexTitle ?? $this->t($locale, 'site.projects.index_title'),
            description: $site->projectsIndexMetaDescription ?? $site->description,
            canonical: route('projects.index'),
        )->toArray();
    }

    /**
     * @param  array<string, mixed>  $project
     * @return array<string, mixed>
     */
    public function forProjectShow(array $project, ?string $locale = null): array
    {
        $site = $this->siteSettingsService->forLocale($locale);

        return $this->build(
            site: $site,
            pageTitle: $project['meta_title'] ?? $project['title'],
            description: $project['meta_description'] ?? $project['excerpt'] ?? $site->description,
            image: $project['banner_url'] ?? null,
            canonical: route('projects.show', $project['slug']),
            ogType: 'article',
        )->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function forUtility(string $title, string $robots = 'noindex, nofollow', ?string $locale = null): array
    {
        $site = $this->siteSettingsService->forLocale($locale);

        return $this->build(
            site: $site,
            pageTitle: $title,
            description: $site->description,
            robots: $robots,
        )->toArray();
    }

    private function build(
        SiteSettingsData $site,
        string $pageTitle,
        ?string $description = null,
        ?string $image = null,
        ?string $canonical = null,
        ?string $robots = null,
        string $ogType = 'website',
        bool $standaloneTitle = false,
    ): SeoData {
        $documentTitle = $standaloneTitle
            ? $pageTitle
            : ($pageTitle !== ''
                ? $pageTitle.$site->titleSeparator.$site->name
                : $site->name);

        $twitterSite = $site->twitterHandle !== null && $site->twitterHandle !== ''
            ? (str_starts_with($site->twitterHandle, '@') ? $site->twitterHandle : '@'.$site->twitterHandle)
            : null;

        return new SeoData(
            title: $documentTitle,
            description: $description,
            keywords: $site->keywords,
            image: $image ?? $site->ogImageUrl ?? $site->logoUrl,
            canonical: $canonical,
            robots: $robots ?? $site->defaultRobots,
            ogType: $ogType,
            twitterSite: $twitterSite,
            googleSiteVerification: $site->googleSiteVerification,
            yandexVerification: $site->yandexVerification,
        );
    }

    private function t(string $locale, string $key): string
    {
        return $this->translationLoader->get($locale, $key);
    }
}
