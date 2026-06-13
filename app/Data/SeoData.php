<?php

namespace App\Data;

readonly class SeoData
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?string $keywords = null,
        public ?string $image = null,
        public ?string $canonical = null,
        public ?string $robots = null,
        public string $ogType = 'website',
        public string $twitterCard = 'summary_large_image',
        public ?string $twitterSite = null,
        public ?string $googleSiteVerification = null,
        public ?string $yandexVerification = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'image' => $this->image,
            'canonical' => $this->canonical,
            'robots' => $this->robots,
            'og_type' => $this->ogType,
            'twitter_card' => $this->twitterCard,
            'twitter_site' => $this->twitterSite,
            'google_site_verification' => $this->googleSiteVerification,
            'yandex_verification' => $this->yandexVerification,
        ];
    }
}
