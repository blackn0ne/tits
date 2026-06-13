<?php

namespace App\Services;

use App\Models\Language;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;

class InertiaSharedData
{
    public function __construct(
        private SiteSettingsService $siteSettingsService,
        private TranslationLoader $translationLoader,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function forRequest(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $site = $this->siteSettingsService->forLocale();

        return [
            'name' => $site->name,
            'site' => $site->toArray(),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'locale' => app()->getLocale(),
            'languages' => Language::query()
                ->orderBy('name')
                ->get(['name', 'code']),
            'i18n' => $this->translationLoader->forLocale(),
            'flash' => [
                'status' => fn () => $this->flashStatus($request),
            ],
        ];
    }

    private function flashStatus(Request $request): ?string
    {
        if (! $request->hasSession()) {
            return null;
        }

        $status = $request->session()->get('status');

        return is_string($status) ? $status : null;
    }
}
