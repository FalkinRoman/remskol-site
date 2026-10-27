@php($items = isset($ids) ? collect($catalog['works'])->whereIn('id', $ids)->sortBy(fn ($w) => array_search($w['id'], $ids))->values() : collect($catalog['works']))
<ul class="works-grid {{ $class ?? '' }}" data-gallery="{{ $group ?? 'works' }}">
    @foreach ($items as $w)
        <li class="works-grid__item">
            <a class="works-grid__link" href="{{ asset('images/works/'.$w['file'].'.jpg') }}"
               data-lightbox-item data-webp="{{ asset('images/works/'.$w['file'].'.webp') }}" data-w="{{ $w['w'] }}" data-h="{{ $w['h'] }}">
                <picture>
                    <source srcset="{{ asset('images/works/'.$w['file'].'-sm.webp') }}" type="image/webp">
                    <img src="{{ asset('images/works/'.$w['file'].'-sm.jpg') }}" alt="{{ $w['alt'] }}" width="480" height="480" loading="lazy" decoding="async">
                </picture>
                <span class="works-grid__zoom" aria-hidden="true">@include('partials.icon', ['name' => 'zoom'])</span>
            </a>
        </li>
    @endforeach
</ul>
