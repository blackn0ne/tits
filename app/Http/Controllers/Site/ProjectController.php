<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Project;
use App\Services\LocalizedContentResolver;
use App\Services\SeoService;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        private LocalizedContentResolver $resolver,
        private SeoService $seoService,
    ) {}

    public function index(): Response
    {
        $locale = app()->getLocale();

        $projects = $this->resolver
            ->constrainToLocale(
                Project::query()
                    ->published()
                    ->with(['translations.language', 'category.translations']),
                $locale,
            )
            ->latest('published_at')
            ->latest('id')
            ->paginate($this->siteProjectsPerPage())
            ->withQueryString()
            ->through(fn (Project $project) => $this->resolver->mapProject($project, $locale));

        $categories = Project::query()
            ->published()
            ->with('category.translations')
            ->get()
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->sortBy('sort_order')
            ->map(fn ($category) => $this->resolver->mapProjectCategory($category, $locale))
            ->filter()
            ->values();

        return Inertia::render('site/Projects/Index', [
            'projects' => $projects,
            'categories' => $categories,
            'seo' => $this->seoService->forProjectsIndex($locale),
        ])->rootView('site');
    }

    public function show(string $slug): Response
    {
        $locale = app()->getLocale();
        $languageId = Language::query()->where('code', $locale)->value('id');

        $project = Project::query()
            ->published()
            ->whereHas('translations', function ($query) use ($slug, $languageId): void {
                $query->where('slug', $slug);

                if ($languageId !== null) {
                    $query->where('language_id', $languageId);
                }
            })
            ->with(['translations.language', 'category.translations'])
            ->firstOrFail();

        $mapped = $this->resolver->mapProject($project, $locale);

        abort_if($mapped === null, 404);

        return Inertia::render('site/Projects/Show', [
            'project' => $mapped,
            'seo' => $this->seoService->forProjectShow($mapped, $locale),
        ])->rootView('site');
    }
}
