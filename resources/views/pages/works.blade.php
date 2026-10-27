@extends('layouts.site')

@section('title', 'Примеры работ: ремонт сколов и трещин лобового стекла — фото | Ремскол')
@section('description', 'Фото наших работ: ремонт сколов и трещин, полировка лобового стекла в Зеленограде. '.count($catalog['works']).' живых фото из мастерской Ремскол на Андреевке, 31б.')
@section('canonical', route('works'))
@section('og_image', asset('images/hero-raboty.jpg'))

@push('head')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'ImageGallery',
        'name' => 'Примеры работ Ремскол',
        'url' => route('works'),
        'image' => collect($catalog['works'])->map(fn ($w) => [
            '@type' => 'ImageObject',
            'contentUrl' => asset('images/works/'.$w['file'].'.jpg'),
            'thumbnailUrl' => asset('images/works/'.$w['file'].'-sm.jpg'),
            'description' => $w['alt'],
        ])->all(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('main')
    @include('partials.page-hero', [
        'image' => 'hero-raboty',
        'title' => 'Примеры работ',
        'lead' => 'Живые фото из нашей мастерской: ремонт сколов и трещин, работа с лобовыми стеклами разных автомобилей. Нажмите на фото, чтобы посмотреть его крупно.',
        'crumbs' => [['name' => 'Примеры работ', 'url' => route('works')]],
    ])

    <section class="works-page">
        <div class="container">
            @include('partials.works-grid', ['group' => 'all', 'class' => 'works-grid--page'])
        </div>
    </section>

@endsection
