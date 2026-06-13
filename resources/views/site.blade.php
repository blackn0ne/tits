<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ $site['name'] ?? config('app.name') }}</title>

        @if (! empty($site['description']))
            <meta name="description" content="{{ $site['description'] }}">
        @endif
        @if (! empty($site['keywords']))
            <meta name="keywords" content="{{ $site['keywords'] }}">
        @endif
        @if (! empty($site['default_robots']))
            <meta name="robots" content="{{ $site['default_robots'] }}">
        @endif
        <meta property="og:title" content="{{ $site['name'] ?? config('app.name') }}">
        @if (! empty($site['description']))
            <meta property="og:description" content="{{ $site['description'] }}">
        @endif
        @if (! empty($site['og_image_url'] ?? $site['logo_url'] ?? null))
            <meta property="og:image" content="{{ $site['og_image_url'] ?? $site['logo_url'] }}">
        @endif

        @if (! empty($site['favicon_url']))
            <link rel="icon" href="{{ $site['favicon_url'] }}">
        @endif

        <link rel="stylesheet" href="{{ asset('site/assets/css/vendor/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('site/assets/css/plugins/fontawesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('site/assets/css/style.css') }}">
        <style>
            html, body { margin: 0; background-color: #F3F4F6; }
            #app { min-height: 100vh; }
            #about, #services, #cases, #blog, #contacts, #shop {
                scroll-margin-top: 100px;
            }
            .blog-section .slide-category__icon,
            .cases-section .slide-category__icon {
                width: 8px;
                height: 8px;
                border: none;
                border-radius: 50%;
                background: #98FF03;
            }
            .blog-section .slide-category__icon > *,
            .cases-section .slide-category__icon > * {
                display: none !important;
            }
            .header-style-one .header-lang-btn {
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.04em;
            }
            .header-style-one .header-lang-btn--active {
                box-shadow: inset 0 0 0 2px #98FF03;
            }
            .office-section__title,
            .blog-section__title,
            .cases-section__title {
                max-width: none;
            }
            .office-section__accent,
            .blog-section__accent,
            .cases-section__accent {
                white-space: nowrap;
            }
            .site-page-section {
                padding-top: 40px;
            }
            .site-cards-grid {
                position: relative;
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0;
                padding: 0 24px 24px;
            }
            .site-cards-grid .blog-card {
                border-right: 1px solid #E5E7EB;
                border-bottom: 1px solid #E5E7EB;
            }
            .site-projects-grid {
                position: relative;
                display: grid;
                grid-template-columns: repeat(1, minmax(0, 1fr));
                gap: 24px;
                padding: 0 24px 24px;
            }
            .site-projects-grid .case-slide {
                border: 1px solid #E5E7EB;
            }
            .site-page-empty {
                padding: 48px 24px 64px;
                text-align: center;
                color: #6B7280;
                font-size: 18px;
            }
            .site-article {
                padding: 32px 24px 48px;
                max-width: 820px;
                min-height: 600px;
            }
            .site-article__meta {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 12px 16px;
                margin-bottom: 12px;
            }
            .site-article__meta .blog-card__meta {
                margin: 0;
            }
            .site-article__back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 24px;
                color: #111827;
                font-weight: 600;
                text-decoration: none;
            }
            .site-article__back:hover {
                color: #98FF03;
            }
            .site-article__title {
                margin: 12px 0 24px;
                font-size: clamp(28px, 4vw, 42px);
                line-height: 1.15;
            }
            .site-article__banner {
                margin-bottom: 28px;
                border-radius: 12px;
                overflow: hidden;
            }
            .site-article__banner img {
                width: 100%;
                height: auto;
                display: block;
            }
            .site-article__content {
                color: #374151;
                font-size: 18px;
                line-height: 1.7;
            }
            .site-article__content p + p {
                margin-top: 16px;
            }
            @media only screen and (max-width: 991px) {
                .site-cards-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }
            @media only screen and (max-width: 575px) {
                .site-cards-grid {
                    grid-template-columns: 1fr;
                    padding: 0 16px 16px;
                }
                .site-projects-grid {
                    padding: 0 16px 16px;
                }
                .site-article {
                    padding: 24px 16px 40px;
                }
            }
        </style>

        @routes
        @vite(['resources/js/site.ts'])
        @inertiaHead
    </head>
    <body class="home-bg overflow-x-visible">
        <script>window.__preloaderStartedAt = Date.now();</script>
        @if (request()->routeIs('home'))
            @include('partials.site-preloader')
        @endif
        @inertia
    </body>
</html>
