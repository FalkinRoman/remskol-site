@extends('layouts.site')

@section('title', 'Цены на ремонт сколов, трещин и полировку лобового стекла в Зеленограде | Ремскол')
@section('description', 'Прайс-лист Ремскол: ремонт скола от 800 ₽, остановка трещины от 500 ₽, заливка трещины от 40 ₽/см, полировка лобового стекла от 5 000 ₽. Точная цена после осмотра или по фото.')
@section('canonical', route('prices'))
@section('og_image', asset('images/hero-ceny.jpg'))

@section('main')
    @include('partials.page-hero', [
        'image' => 'hero-ceny',
        'title' => 'Цены',
        'lead' => 'Указаны цены «от»: итоговая стоимость зависит от размера, глубины и загрязнённости повреждения. Точную цену мастер назовёт после осмотра — или ориентировочно по фото в мессенджере.',
        'crumbs' => [['name' => 'Цены', 'url' => route('prices')]],
    ])

    <section class="prices-page">
        <div class="container prices-page__grid">
            @foreach ($catalog['services'] as $slug => $s)
                @include('partials.price-table', [
                    'heading' => $s['title'],
                    'headingTag' => 'h2',
                    'href' => route('service', $slug),
                    'prices' => $s['prices'],
                    'service' => $s['title'],
                ])
            @endforeach
        </div>
        <div class="container">
            <p class="prices-page__note">{{ nb('Цены носят справочный характер и не являются публичной офертой. Стоимость работ согласовываем до начала ремонта.') }}</p>
        </div>
    </section>

@endsection
