<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\SitemapService;
use Illuminate\Http\Response;

class SiteSeoController extends Controller
{
    public function robots(SitemapService $sitemapService): Response
    {
        return response($sitemapService->robotsTxt(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function sitemap(SitemapService $sitemapService): Response
    {
        if (! $sitemapService->isEnabled()) {
            abort(404);
        }

        return response($sitemapService->toXml(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
