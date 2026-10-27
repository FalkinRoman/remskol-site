<!DOCTYPE html>
<html lang="ru" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $site['seo']['title'])</title>
    <meta name="description" content="@yield('description', $site['seo']['description'])">
    <meta name="keywords" content="{{ $site['seo']['keywords'] }}">
    <meta name="robots" content="@yield('robots', app()->isProduction() ? 'index, follow, max-image-preview:large' : 'noindex, nofollow')">
    <link rel="canonical" href="@yield('canonical', url('/'))">
    <meta name="format-detection" content="telephone=yes">
    <meta name="theme-color" content="#000000">

    @if ($site['yandex_verification'])
        <meta name="yandex-verification" content="{{ $site['yandex_verification'] }}">
    @endif
    @if ($site['google_verification'])
        <meta name="google-site-verification" content="{{ $site['google_verification'] }}">
    @endif

    <meta property="og:type" content="website">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:site_name" content="{{ $site['brand_plain'] }}">
    <meta property="og:title" content="@yield('title', $site['seo']['title'])">
    <meta property="og:description" content="@yield('description', $site['seo']['description'])">
    <meta property="og:url" content="@yield('canonical', url('/'))">
    <meta property="og:image" content="@yield('og_image', asset($site['seo']['og_image']))">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon-32.png') }}" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600&family=Roboto:wght@300;400;500;700&display=swap">

    @stack('preload')

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">

    @stack('head')
</head>
<body>
    @yield('content')

    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>

    @if ($site['metrika_id'] && app()->isProduction())
        <script>
            (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
            ym({{ (int) $site['metrika_id'] }}, "init", {clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true});
            window.SITE_METRIKA_ID = {{ (int) $site['metrika_id'] }};
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/{{ (int) $site['metrika_id'] }}" style="position:absolute; left:-9999px;" alt=""></div></noscript>
    @endif
</body>
</html>
