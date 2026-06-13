<?php

namespace App\Data;

readonly class SiteSettingsData
{
    /**
     * @param  array<string, string|null>  $social
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public ?string $keywords,
        public ?string $logoUrl,
        public ?string $faviconUrl,
        public ?string $phone,
        public ?string $address,
        public array $social,
        public string $titleSeparator,
        public string $defaultRobots,
        public ?string $ogImageUrl,
        public ?string $twitterHandle,
        public ?string $googleSiteVerification,
        public ?string $yandexVerification,
        public bool $sitemapEnabled,
        public ?string $robotsTxt,
        public ?string $homeTitle,
        public ?string $homeMetaDescription,
        public ?string $blogIndexTitle,
        public ?string $blogIndexMetaDescription,
        public ?string $projectsIndexTitle,
        public ?string $projectsIndexMetaDescription,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'logo_url' => $this->logoUrl,
            'favicon_url' => $this->faviconUrl,
            'phone' => $this->phone,
            'address' => $this->address,
            'social' => $this->social,
            'title_separator' => $this->titleSeparator,
            'default_robots' => $this->defaultRobots,
            'og_image_url' => $this->ogImageUrl,
            'twitter_handle' => $this->twitterHandle,
            'google_site_verification' => $this->googleSiteVerification,
            'yandex_verification' => $this->yandexVerification,
            'sitemap_enabled' => $this->sitemapEnabled,
            'robots_txt' => $this->robotsTxt,
            'home_title' => $this->homeTitle,
            'home_meta_description' => $this->homeMetaDescription,
            'blog_index_title' => $this->blogIndexTitle,
            'blog_index_meta_description' => $this->blogIndexMetaDescription,
            'projects_index_title' => $this->projectsIndexTitle,
            'projects_index_meta_description' => $this->projectsIndexMetaDescription,
        ];
    }
}
