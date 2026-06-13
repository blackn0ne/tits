<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use App\Services\TranslationLoader;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class UtilityPageController extends Controller
{
    public function __construct(
        private SeoService $seoService,
        private TranslationLoader $translationLoader,
    ) {}

    public function notFound(Request $request): Response
    {
        $locale = app()->getLocale();
        $title = $this->translationLoader->get($locale, 'site.not_found.title');

        return Inertia::render('site/NotFound', [
            'seo' => $this->seoService->forUtility($title, 'noindex, nofollow', $locale),
        ])
            ->rootView('site')
            ->toResponse($request)
            ->setStatusCode(404);
    }

    public function maintenance(Request $request): Response
    {
        $locale = app()->getLocale();
        $title = $this->translationLoader->get($locale, 'site.maintenance.title');

        return Inertia::render('site/Maintenance', [
            'seo' => $this->seoService->forUtility($title, 'noindex, nofollow', $locale),
        ])
            ->rootView('site')
            ->toResponse($request);
    }
}
