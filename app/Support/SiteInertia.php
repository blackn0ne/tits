<?php

namespace App\Support;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renders site Inertia pages outside the normal controller flow
 * (e.g. framework-level 503 responses) where web middleware may not run.
 */
class SiteInertia
{
    public static function share(Request $request): void
    {
        $middleware = app(HandleInertiaRequests::class);

        Inertia::share($middleware->share($request));
        Inertia::version(fn () => $middleware->version($request));
        Inertia::setRootView('site');
    }

    /**
     * @param  array<string, mixed>  $props
     */
    public static function render(string $component, Request $request, array $props = [], int $status = 200): Response
    {
        self::share($request);

        return Inertia::render($component, $props)
            ->rootView('site')
            ->toResponse($request)
            ->setStatusCode($status);
    }
}
