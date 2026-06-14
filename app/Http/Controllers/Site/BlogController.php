<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Language;
use App\Services\LocalizedContentResolver;
use App\Services\SeoService;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function __construct(
        private LocalizedContentResolver $resolver,
        private SeoService $seoService,
    ) {}

    public function index(): Response
    {
        $locale = app()->getLocale();

        $posts = $this->resolver
            ->constrainToLocale(
                BlogPost::query()
                    ->published()
                    ->with(['translations.language', 'category.translations']),
                $locale,
            )
            ->latest('published_at')
            ->latest('id')
            ->paginate($this->siteBlogPerPage())
            ->withQueryString()
            ->through(fn (BlogPost $post) => $this->resolver->mapBlogPost($post, $locale));

        $categories = BlogPost::query()
            ->published()
            ->with('category.translations')
            ->get()
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->sortBy('sort_order')
            ->map(fn ($category) => $this->resolver->mapBlogCategory($category, $locale))
            ->filter()
            ->values();

        return Inertia::render('site/Blog/Index', [
            'posts' => $posts,
            'categories' => $categories,
            'seo' => $this->seoService->forBlogIndex($locale),
        ])->rootView('site');
    }

    public function show(string $slug): Response
    {
        $locale = app()->getLocale();
        $languageId = Language::query()->where('code', $locale)->value('id');

        $post = BlogPost::query()
            ->published()
            ->whereHas('translations', function ($query) use ($slug, $languageId): void {
                $query->where('slug', $slug);

                if ($languageId !== null) {
                    $query->where('language_id', $languageId);
                }
            })
            ->with(['translations.language', 'category.translations'])
            ->firstOrFail();

        $mapped = $this->resolver->mapBlogPost($post, $locale);

        abort_if($mapped === null, 404);

        return Inertia::render('site/Blog/Show', [
            'post' => $mapped,
            'seo' => $this->seoService->forBlogShow($mapped, $locale),
        ])->rootView('site');
    }
}
