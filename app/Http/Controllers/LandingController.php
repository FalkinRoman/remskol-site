<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        return view('landing', [
            'site' => config('site'),
        ]);
    }

    public function privacy(): View
    {
        return view('privacy', [
            'site' => config('site'),
        ]);
    }

    public function consent(): View
    {
        return view('consent', [
            'site' => config('site'),
        ]);
    }

    public function sitemap(): Response
    {
        $mtime = fn (string ...$files) => date('Y-m-d', max(array_map('filemtime', $files)));
        $catalog = config('catalog');
        $workImage = fn (array $w) => ['loc' => asset('images/works/'.$w['file'].'.jpg'), 'title' => $w['alt']];

        $urls = [
            ['loc' => route('home'), 'lastmod' => $mtime(resource_path('views/landing.blade.php'), config_path('site.php')), 'priority' => '1.0', 'changefreq' => 'weekly'],
        ];

        foreach ($catalog['services'] as $slug => $s) {
            $urls[] = [
                'loc' => route('service', $slug),
                'lastmod' => $mtime(resource_path('views/pages/service.blade.php'), config_path('catalog.php')),
                'priority' => '0.9',
                'changefreq' => 'monthly',
                'images' => collect($catalog['works'])->whereIn('id', $s['gallery'])->map($workImage)->values()->all(),
            ];
        }

        $urls[] = ['loc' => route('prices'), 'lastmod' => $mtime(resource_path('views/pages/prices.blade.php'), config_path('catalog.php')), 'priority' => '0.8', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => route('works'), 'lastmod' => $mtime(resource_path('views/pages/works.blade.php'), config_path('catalog.php')), 'priority' => '0.7', 'changefreq' => 'monthly', 'images' => array_map($workImage, $catalog['works'])];
        $urls[] = ['loc' => route('contacts'), 'lastmod' => $mtime(resource_path('views/pages/contacts.blade.php'), config_path('site.php')), 'priority' => '0.7', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => route('privacy'), 'lastmod' => $mtime(resource_path('views/privacy.blade.php')), 'priority' => '0.2', 'changefreq' => 'yearly'];
        $urls[] = ['loc' => route('consent'), 'lastmod' => $mtime(resource_path('views/consent.blade.php')), 'priority' => '0.2', 'changefreq' => 'yearly'];

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = app()->isProduction()
            ? [
                'User-agent: *',
                'Disallow: /lead',
                'Disallow: /telegram/',
                'Disallow: /*?',
                'Allow: /*?utm_',
                'Allow: /*?yclid=',
                'Allow: /*?gclid=',
                '',
                'Clean-param: utm_source&utm_medium&utm_campaign&utm_content&utm_term&yclid&gclid&fbclid&_openstat /',
                '',
                'Sitemap: '.url('/sitemap.xml'),
                'Host: '.url('/'),
            ]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
