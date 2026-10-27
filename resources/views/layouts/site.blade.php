@extends('layouts.app')

@section('content')
    <a class="skip-link" href="#main">Перейти к содержимому</a>

    @include('partials.header')

    <main id="main">
        @yield('main')
    </main>

    @include('partials.footer')
    @include('partials.fab')
    @include('partials.cookie')
    @include('partials.lead-modal')

    <dialog class="lightbox" aria-label="Просмотр фото" data-lightbox>
        <figure class="lightbox__figure">
            <img class="lightbox__img" alt="" data-lightbox-img>
            <figcaption class="lightbox__caption"><span data-lightbox-alt></span> <span class="lightbox__count" data-lightbox-count></span></figcaption>
        </figure>
        <button class="lightbox__btn lightbox__btn--close" type="button" aria-label="Закрыть" data-lightbox-close>@include('partials.icon', ['name' => 'close'])</button>
        <button class="lightbox__btn lightbox__btn--prev" type="button" aria-label="Предыдущее фото" data-lightbox-prev>@include('partials.icon', ['name' => 'arrow-left'])</button>
        <button class="lightbox__btn lightbox__btn--next" type="button" aria-label="Следующее фото" data-lightbox-next>@include('partials.icon', ['name' => 'arrow-right'])</button>
    </dialog>
@endsection
