<?php

namespace App\Http\Middleware;

use App\Services\SiteSettingsService;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteIsNotInMaintenance
{
    public function __construct(private SiteSettingsService $siteSettingsService) {}

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->siteSettingsService->isMaintenanceMode()) {
            return $next($request);
        }

        if ($this->siteSettingsService->shouldBypassMaintenance($request)) {
            return $next($request);
        }

        return Inertia::render('site/Maintenance')
            ->rootView('site')
            ->toResponse($request)
            ->setStatusCode(503);
    }
}
