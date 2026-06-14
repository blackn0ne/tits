<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Project;
use App\Services\LocalizedContentResolver;
use App\Services\SeoService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        private LocalizedContentResolver $resolver,
        private SeoService $seoService,
    ) {}

    public function __invoke(): Response
    {
        $locale = app()->getLocale();

        $posts = BlogPost::query()
            ->published()
            ->with(['translations.language', 'category.translations'])
            ->latest('published_at')
            ->latest('id')
            ->limit(6)
            ->get()
            ->map(fn (BlogPost $post) => $this->resolver->mapBlogPost($post, $locale))
            ->filter()
            ->values();

        $projects = $this->resolver
            ->constrainToLocale(
                Project::query()
                    ->published()
                    ->with(['translations.language', 'category.translations']),
                $locale,
            )
            ->latest('published_at')
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn (Project $project) => $this->resolver->mapProject($project, $locale))
            ->filter()
            ->values();

        return Inertia::render('site/Home', [
            'posts' => $posts,
            'projects' => $projects,
            'seo' => $this->seoService->forHome($locale),
        ])->rootView('site');
    }
}
