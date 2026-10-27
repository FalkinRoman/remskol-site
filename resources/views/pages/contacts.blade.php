@extends('layouts.site')

@section('title', 'Контакты — ремонт лобовых стекол в Зеленограде, Андреевка 31б | Ремскол')
@section('description', 'Ремскол: г. Зеленоград, Андреевка, 31б. Телефон '.$site['phone'].', Telegram и WhatsApp. Режим работы: '.$site['hours_long'].', '.mb_strtolower($site['day_off']).'.')
@section('canonical', route('contacts'))

@include('partials.schema-org')

@section('main')
    @php($tel = 'tel:'.$site['phone_href'])
    @include('partials.page-hero', [
        'title' => 'Контакты',
        'lead' => 'Приезжайте по записи — так не придётся ждать. Записаться можно по телефону, в мессенджерах или через форму на сайте.',
        'crumbs' => [['name' => 'Контакты', 'url' => route('contacts')]],
        'actions' => false,
    ])

    <section class="contacts-page">
        <div class="container contacts-page__grid">
            <div class="contact-cards">
                <a class="contact-card" href="{{ $tel }}" data-goal="call_click">
                    <span class="contact-card__icon">@include('partials.icon', ['name' => 'phone'])</span>
                    <span><span class="contact-card__label">Телефон</span><span class="contact-card__value">{{ $site['phone'] }}</span></span>
                </a>
                <div class="contact-card">
                    <span class="contact-card__icon">@include('partials.icon', ['name' => 'pin'])</span>
                    <span><span class="contact-card__label">Адрес</span><span class="contact-card__value">{{ $site['address_full'] }}</span>
                        <a class="contact-card__link" href="{{ $site['map_link'] }}" target="_blank" rel="noopener">Построить маршрут в Яндекс.Картах →</a></span>
                </div>
                <div class="contact-card">
                    <span class="contact-card__icon">@include('partials.icon', ['name' => 'clock'])</span>
                    <span><span class="contact-card__label">Режим работы</span><span class="contact-card__value">{{ $site['hours_long'] }}</span><span class="contact-card__muted">{{ $site['day_off'] }}</span></span>
                </div>
                <div class="contact-card contact-card--msgs">
                    <span class="contact-card__label">Мессенджеры</span>
                    <div class="footer__msgs">
                        <a class="footer__msg footer__msg--tg" href="{{ $site['telegram'] }}" target="_blank" rel="noopener" data-goal="messenger_click">@include('partials.icon', ['name' => 'telegram']) Telegram</a>
                        <a class="footer__msg footer__msg--wa" href="{{ $site['whatsapp'] }}" target="_blank" rel="noopener" data-goal="messenger_click">@include('partials.icon', ['name' => 'whatsapp']) WhatsApp</a>
                    </div>
                </div>
                <div class="requisites">
                    <h2 class="requisites__title">Реквизиты</h2>
                    <dl>
                        <dt>Исполнитель</dt><dd>{{ $site['legal']['status'] }} {{ $site['legal']['name'] }}</dd>
                        <dt>ИНН</dt><dd>{{ $site['legal']['inn'] }}</dd>
                        @if ($site['legal']['ogrnip'])
                            <dt>ОГРНИП</dt><dd>{{ $site['legal']['ogrnip'] }}</dd>
                        @endif
                        @if ($site['legal']['email'])
                            <dt>E-mail</dt><dd><a href="mailto:{{ $site['legal']['email'] }}">{{ $site['legal']['email'] }}</a></dd>
                        @endif
                    </dl>
                </div>
            </div>
            <div class="contacts-page__map">
                <iframe src="{{ $site['map_iframe'] }}" title="Карта: {{ $site['address_full'] }}" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
@endsection
