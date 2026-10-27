@php($crumbs = array_merge([['name' => 'Главная', 'url' => route('home')]], $items))
<nav class="crumbs" aria-label="Хлебные крошки">
    <ol>
        @foreach ($crumbs as $c)
            <li>
                @if (! $loop->last)
                    <a href="{{ $c['url'] }}">{{ $c['name'] }}</a>
                @else
                    <span aria-current="page">{{ $c['name'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

@push('head')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($crumbs)->values()->map(fn ($c, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $c['name'],
            'item' => $c['url'],
        ])->all(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush
