<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Language;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\URL;

class SitemapService
{
    public function __construct(
        private LocalizedContentResolver $resolver,
    ) {}

    public function isEnabled(): bool
    {
        return (bool) SiteSetting::query()->value('sitemap_enabled');
    }

    public function toXml(): string
    {
        $urls = [];

        $urls[] = $this->entry(route('home'), now(), 'weekly', '1.0');
        $urls[] = $this->entry(route('blog.index'), now(), 'weekly', '0.8');
        $urls[] = $this->entry(route('projects.index'), now(), 'weekly', '0.8');

        BlogPost::query()
            ->published()
            ->with(['translations.language'])
            ->latest('published_at')
            ->get()
            ->each(function (BlogPost $post) use (&$urls): void {
                $mapped = $this->resolver->mapBlogPost($post, app()->getLocale());

                if ($mapped === null) {
                    return;
                }

                $urls[] = $this->entry(
                    route('blog.show', $mapped['slug']),
                    $post->updated_at,
                    'monthly',
                    '0.7',
                );
            });

        Project::query()
            ->published()
            ->with(['translations.language'])
            ->latest('published_at')
            ->get()
            ->each(function (Project $project) use (&$urls): void {
                $mapped = $this->resolver->mapProject($project, app()->getLocale());

                if ($mapped === null) {
                    return;
                }

                $urls[] = $this->entry(
                    route('projects.show', $mapped['slug']),
                    $project->updated_at,
                    'monthly',
                    '0.7',
                );
            });

        $body = implode('', $urls);

        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .$body
            .'</urlset>';
    }

    public function robotsTxt(): string
    {
        $custom = SiteSetting::query()->value('robots_txt');

        if (is_string($custom) && trim($custom) !== '') {
            return trim($custom);
        }

        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /dashboard',
            'Disallow: /login',
            'Disallow: /settings',
        ];

        if ($this->isEnabled()) {
            $lines[] = 'Sitemap: '.URL::to('/sitemap.xml');
        }

        return implode(PHP_EOL, $lines).PHP_EOL;
    }

    /**
     * @param  \Illuminate\Support\Carbon|\DateTimeInterface|string  $lastModified
     */
    private function entry(string $loc, $lastModified, string $changefreq, string $priority): string
    {
        $lastmod = $lastModified instanceof \DateTimeInterface
            ? $lastModified->format('Y-m-d')
            : now()->format('Y-m-d');

        return '<url>'
            .'<loc>'.e($loc).'</loc>'
            .'<lastmod>'.$lastmod.'</lastmod>'
            .'<changefreq>'.$changefreq.'</changefreq>'
            .'<priority>'.$priority.'</priority>'
            .'</url>';
    }
}
