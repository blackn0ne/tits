<?php

namespace App\Providers;

use App\Models\BlogCategoryTranslation;
use App\Models\BlogPostTranslation;
use App\Models\ProjectCategoryTranslation;
use App\Models\ProjectTranslation;
use App\Observers\BlogCategoryTranslationObserver;
use App\Observers\BlogPostTranslationObserver;
use App\Observers\ProjectCategoryTranslationObserver;
use App\Observers\ProjectTranslationObserver;
use App\Services\DashboardSummaryService;
use App\Services\InertiaSharedData;
use App\Services\SalesReportService;
use App\Services\SeoService;
use App\Services\SitemapService;
use App\Services\SiteSettingsService;
use App\Services\TranslationLoader;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TranslationLoader::class);
        $this->app->singleton(SiteSettingsService::class);
        $this->app->singleton(InertiaSharedData::class);
        $this->app->singleton(SalesReportService::class);
        $this->app->singleton(DashboardSummaryService::class);
        $this->app->singleton(SeoService::class);
        $this->app->singleton(SitemapService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BlogCategoryTranslation::observe(BlogCategoryTranslationObserver::class);
        BlogPostTranslation::observe(BlogPostTranslationObserver::class);
        ProjectCategoryTranslation::observe(ProjectCategoryTranslationObserver::class);
        ProjectTranslation::observe(ProjectTranslationObserver::class);

        $shareSiteSettings = function ($view): void {
            $view->with('site', app(SiteSettingsService::class)->forLocale(app()->getLocale())->toArray());
        };

        View::composer(['app', 'site'], $shareSiteSettings);
    }
}
