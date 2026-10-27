@extends('layouts.site')

@section('title', $s['seo_title'])
@section('description', $s['seo_description'])
@section('canonical', route('service', $slug))
@section('og_image', asset('images/'.$s['hero'].'.jpg'))

@push('head')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $s['h1'],
        'serviceType' => $s['title'],
        'description' => $s['seo_description'],
        'url' => route('service', $slug),
        'image' => asset('images/'.$s['hero'].'.jpg'),
        'areaServed' => ['@type' => 'City', 'name' => $site['city']],
        'provider' => ['@type' => 'AutoRepair', '@id' => url('/').'#org', 'name' => $site['brand_plain'], 'telephone' => $site['phone_href'], 'address' => $site['address_full']],
        'offers' => [
            '@type' => 'AggregateOffer',
            'priceCurrency' => 'RUB',
            'lowPrice' => $s['price_min'],
            'offerCount' => count($s['prices']),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('main')
    @include('partials.page-hero', [
        'image' => $s['hero'],
        'title' => $s['h1'],
        'lead' => $s['lead'],
        'facts' => $s['facts'],
        'service' => $s['title'],
        'crumbs' => [['name' => 'Услуги', 'url' => route('home').'#services'], ['name' => $s['title'], 'url' => route('service', $slug)]],
    ])

    <section class="svc-intro">
        <div class="container svc-intro__grid">
            <div class="svc-intro__text prose">
                <h2 class="h2">{{ nb($s['intro_title']) }}</h2>
                @foreach ($s['intro'] as $p)
                    <p>{{ nb($p) }}</p>
                @endforeach
                <figure class="svc-intro__img">
                    @include('partials.picture', ['name' => $s['intro_image']['name'], 'alt' => $s['intro_image']['alt'], 'width' => $s['intro_image']['w'], 'height' => $s['intro_image']['h']])
                </figure>
            </div>
            <aside class="tip-card">
                <h2 class="tip-card__title">{{ nb($s['tip_title']) }}</h2>
                <ul class="checklist">
                    @foreach ($s['tips'] as $tip)
                        <li>@include('partials.icon', ['name' => 'check']) <span>{{ nb($tip) }}</span></li>
                    @endforeach
                </ul>
                <a class="tip-card__btn" href="{{ $site['whatsapp'] }}" target="_blank" rel="noopener" data-goal="messenger_click">@include('partials.icon', ['name' => 'whatsapp']) Отправить фото в WhatsApp</a>
                <a class="tip-card__btn tip-card__btn--tg" href="{{ $site['telegram'] }}" target="_blank" rel="noopener" data-goal="messenger_click">@include('partials.icon', ['name' => 'telegram']) Отправить фото в Telegram</a>
            </aside>
        </div>
    </section>

    <section class="steps-section" aria-labelledby="steps-title">
        <div class="container">
            <div class="section-head">
                <h2 class="section-title" id="steps-title">{{ nb($s['steps_title']) }}</h2>
            </div>
            <ol class="steps">
                @foreach ($s['steps'] as $step)
                    <li class="step">
                        <h3 class="step__title">{{ $step['title'] }}</h3>
                        <p class="step__text">{{ nb($step['text']) }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="svc-result" id="ceny" aria-labelledby="price-title">
        <div class="container svc-result__grid">
            <figure class="before-after">
                @include('partials.picture', ['name' => $s['before_after']['name'], 'alt' => $s['before_after']['alt'], 'width' => $s['before_after']['w'], 'height' => $s['before_after']['h']])
                <figcaption>{{ $s['before_after']['alt'] }}</figcaption>
            </figure>
            <div>
                <h2 class="h2" id="price-title">{{ $s['price_title'] }}</h2>
                @include('partials.price-table', ['prices' => $s['prices'], 'note' => $s['price_note'], 'service' => $s['title']])
            </div>
        </div>
    </section>

    <section class="svc-works" aria-labelledby="svc-works-title">
        <div class="container">
            <div class="section-head">
                <h2 class="section-title" id="svc-works-title">Наши работы</h2>
            </div>
            @include('partials.works-grid', ['ids' => $s['gallery'], 'group' => $slug, 'class' => 'works-grid--3'])
            <div class="works-preview__more">
                <a class="btn btn--blue" href="{{ route('works') }}">Все примеры работ</a>
            </div>
        </div>
    </section>

    <section class="faq-section" aria-labelledby="faq-title">
        <div class="container faq-section__inner">
            <div class="section-head">
                <h2 class="section-title" id="faq-title">Частые вопросы</h2>
            </div>
            @include('partials.faq', ['faq' => $s['faq']])
        </div>
    </section>

    <section class="other-services" aria-labelledby="other-title">
        <div class="container">
            <h2 class="h2" id="other-title">Другие услуги</h2>
            <div class="mini-cards">
                @foreach ($site['services'] as $card)
                    @continue($card['slug'] === $slug)
                    <a class="mini-card" href="{{ route('service', $card['slug']) }}">
                        @include('partials.picture', ['name' => $card['image'], 'alt' => $card['alt'], 'width' => 800, 'height' => 450, 'class' => 'mini-card__img'])
                        <span class="mini-card__body">
                            <span class="mini-card__title">{{ $card['title'] }}</span>
                            <span class="mini-card__price">от {{ number_format($catalog['services'][$card['slug']]['price_min'], 0, '', ' ') }} ₽</span>
                        </span>
                        <span class="mini-card__arrow" aria-hidden="true">@include('partials.icon', ['name' => 'arrow-right'])</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

@endsection
