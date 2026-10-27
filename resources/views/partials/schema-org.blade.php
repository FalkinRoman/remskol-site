@push('head')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'AutoRepair',
        '@id' => url('/').'#org',
        'name' => $site['brand_plain'],
        'url' => url('/'),
        'image' => asset($site['seo']['og_image']),
        'logo' => asset('icon-512.png'),
        'description' => $site['seo']['description'],
        'telephone' => $site['phone_href'],
        'taxID' => $site['legal']['inn'],
        'priceRange' => 'от 500 ₽',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $site['address_short'],
            'addressLocality' => $site['city'],
            'addressRegion' => 'Москва',
            'addressCountry' => 'RU',
        ],
        'openingHoursSpecification' => [[
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => $site['opening_days'],
            'opens' => $site['opens'],
            'closes' => $site['closes'],
        ]],
        'areaServed' => ['@type' => 'City', 'name' => $site['city']],
        'sameAs' => array_values(array_filter([$site['telegram'], $site['whatsapp']])),
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'Услуги',
            'itemListElement' => collect($catalog['services'])->map(fn ($s, $slug) => [
                '@type' => 'Offer',
                'priceSpecification' => ['@type' => 'PriceSpecification', 'minPrice' => $s['price_min'], 'priceCurrency' => 'RUB'],
                'itemOffered' => ['@type' => 'Service', 'name' => $s['title'], 'url' => route('service', $slug)],
            ])->values()->all(),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush
