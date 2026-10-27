<div class="faq">
    @foreach ($faq as $item)
        <details class="faq__item" @if ($loop->first) open @endif>
            <summary class="faq__q">{{ nb($item['q']) }}</summary>
            <div class="faq__a"><p>{{ nb($item['a']) }}</p></div>
        </details>
    @endforeach
</div>

@push('head')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($faq)->map(fn ($i) => [
            '@type' => 'Question',
            'name' => $i['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $i['a']],
        ])->all(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush
