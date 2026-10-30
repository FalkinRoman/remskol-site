@extends('layouts.site')

@push('preload')
    <link rel="preload" as="image" href="{{ img_asset('images/slide-1-m.webp') }}" type="image/webp" media="(max-width: 767px)" fetchpriority="high">
    <link rel="preload" as="image" href="{{ img_asset('images/slide-1.webp') }}" type="image/webp" media="(min-width: 768px)" fetchpriority="high">
@endpush

@include('partials.schema-org')

@section('main')
    {{-- ===== HERO SLIDER ===== --}}
    <section class="slider" aria-roledescription="carousel" aria-label="Наши предложения" data-slider>
        <div class="slider__track">
            @foreach ($site['slides'] as $i => $slide)
                <div class="slide {{ $i === 0 ? 'is-active' : '' }}" role="group" aria-roledescription="slide" aria-label="{{ $i + 1 }} из {{ count($site['slides']) }}" @if ($i) aria-hidden="true" @endif data-slide>
                    <picture class="slide__media">
                        <source media="(max-width: 767px)" srcset="{{ img_asset('images/'.$slide['image'].'-m.webp') }}" type="image/webp">
                        <source srcset="{{ img_asset('images/'.$slide['image'].'.webp') }}" type="image/webp">
                        <img src="{{ img_asset('images/'.$slide['image'].'.jpg') }}" alt="{{ $slide['alt'] }}"
                             width="{{ $slide['size'][0] }}" height="{{ $slide['size'][1] }}"
                             @if ($i === 0) fetchpriority="high" @else loading="lazy" fetchpriority="low" @endif decoding="async">
                    </picture>
                    <div class="slide__overlay" aria-hidden="true"></div>
                    <div class="slide__content">
                        <div class="slide__caption">
                            @if ($i === 0)
                                <h1 class="slide__title">{{ nb($slide['title']) }}</h1>
                            @else
                                <p class="slide__title">{{ nb($slide['title']) }}</p>
                            @endif
                            <p class="slide__text">{{ nb($slide['text']) }}</p>
                        </div>
                        <a class="btn btn--red slide__cta" href="#lead" data-open-lead
                           @if ($slide['comment']) data-comment="{{ $slide['comment'] }}" @endif
                           @if ($i) tabindex="-1" @endif>{{ $slide['cta'] }}</a>
                    </div>
                </div>
            @endforeach
        </div>

        <button class="slider__arrow slider__arrow--prev" type="button" aria-label="Предыдущий слайд" data-slider-prev>@include('partials.icon', ['name' => 'arrow-left'])</button>
        <button class="slider__arrow slider__arrow--next" type="button" aria-label="Следующий слайд" data-slider-next>@include('partials.icon', ['name' => 'arrow-right'])</button>

        <div class="slider__dots" role="tablist" aria-label="Выбор слайда">
            @foreach ($site['slides'] as $i => $slide)
                <button class="slider__dot {{ $i === 0 ? 'is-active' : '' }}" type="button" role="tab" aria-label="Слайд {{ $i + 1 }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" data-slider-dot="{{ $i }}"><span></span></button>
            @endforeach
        </div>
    </section>

    {{-- ===== FEATURES ===== --}}
    <section class="features" aria-label="Преимущества">
        <div class="container features__grid">
            @foreach ($site['features'] as $f)
                <div class="feature">
                    <span class="feature__icon">@include('partials.icon', ['name' => $f['icon']])</span>
                    <div>
                        <p class="feature__title">{{ $f['title'] }}</p>
                        <p class="feature__text">{{ nb($f['text']) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===== SERVICES ===== --}}
    <section class="services" id="services" aria-labelledby="services-title">
        <div class="container">
            <div class="section-head">
                <h2 class="section-title" id="services-title">Наши услуги</h2>
                <p class="section-sub">{{ nb('Работаем с лобовыми стеклами любых автомобилей в Зеленограде') }}</p>
            </div>

            <div class="services__grid">
                @foreach ($site['services'] as $service)
                    <article class="service-card" id="{{ $service['slug'] }}">
                        <a class="service-card__media" href="{{ route('service', $service['slug']) }}" tabindex="-1" aria-hidden="true">
                            @include('partials.picture', ['name' => $service['image'], 'alt' => $service['alt'], 'width' => 800, 'height' => 450, 'class' => 'service-card__img'])
                            <span class="service-card__badge">{{ $service['badge'] }}</span>
                        </a>
                        <div class="service-card__body">
                            <h3 class="service-card__title"><a href="{{ route('service', $service['slug']) }}">{{ $service['title'] }}</a></h3>
                            <p class="service-card__text">{{ nb($service['text']) }}</p>
                            <div class="service-card__actions">
                                <a class="btn btn--red" href="#lead" data-open-lead data-service="{{ $service['title'] }}">Записаться</a>
                                <a class="btn btn--blue" href="{{ route('service', $service['slug']) }}">Подробнее</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== WORKS PREVIEW ===== --}}
    <section class="works-preview" aria-labelledby="works-title">
        <div class="container">
            <div class="section-head">
                <h2 class="section-title" id="works-title">Примеры работ</h2>
                <p class="section-sub">{{ nb('Живые фото из нашей мастерской — без ретуши') }}</p>
            </div>
            @include('partials.works-grid', ['ids' => [1, 2, 3, 4, 5, 6, 7, 8], 'group' => 'home'])
            <div class="works-preview__more">
                <a class="btn btn--blue" href="{{ route('works') }}">Смотреть все работы</a>
            </div>
        </div>
    </section>

@endsection
