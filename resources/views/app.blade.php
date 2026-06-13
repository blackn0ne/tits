<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $site['description'] ?? '' }}">
        <meta name="keywords" content="{{ $site['keywords'] ?? '' }}">

        <title inertia>{{ $site['name'] ?? config('app.name', 'Laravel') }}</title>

        @if (! empty($site['favicon_url']))
            <link rel="icon" href="{{ $site['favicon_url'] }}">
        @endif

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
