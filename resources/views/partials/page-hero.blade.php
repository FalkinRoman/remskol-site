@if (! empty($image))
    @push('preload')
        <link rel="preload" as="image" href="{{ img_asset('images/'.$image.'-m.webp') }}" type="image/webp" media="(max-width: 767px)" fetchpriority="high">
        <link rel="preload" as="image" href="{{ img_asset('images/'.$image.'.webp') }}" type="image/webp" media="(min-width: 768px)" fetchpriority="high">
    @endpush
@endif
<section class="page-hero {{ empty($image) ? 'page-hero--plain' : '' }}">
    @if (! empty($image))
        <picture class="page-hero__bg" aria-hidden="true">
            <source media="(max-width: 767px)" srcset="{{ img_asset('images/'.$image.'-m.webp') }}" type="image/webp">
            <source srcset="{{ img_asset('images/'.$image.'.webp') }}" type="image/webp">
            <img src="{{ img_asset('images/'.$image.'.jpg') }}" alt="" width="1600" height="640" fetchpriority="high" decoding="async">
        </picture>
    @endif
    <div class="container page-hero__inner">
        @include('partials.breadcrumbs', ['items' => $crumbs])
        <h1 class="page-hero__title">{{ nb($title) }}</h1>
        @if (! empty($lead))
            <p class="page-hero__lead">{{ nb($lead) }}</p>
        @endif
        @if (! empty($facts))
            <ul class="page-hero__facts">
                @foreach ($facts as $f)
                    <li>
                        <span class="page-hero__fact-icon">@include('partials.icon', ['name' => $f['icon']])</span>
                        <span><b>{{ $f['title'] }}</b> {{ $f['text'] }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
        @if ($actions ?? true)
            <div class="page-hero__actions">
                <a class="btn btn--red" href="#lead" data-open-lead @isset($service) data-service="{{ $service }}" @endisset>Записаться</a>
                <a class="btn page-hero__ghost" href="{{ $site['telegram'] }}" target="_blank" rel="noopener" data-goal="messenger_click">@include('partials.icon', ['name' => 'camera']) Отправить фото</a>
            </div>
        @endif
    </div>
</section>
