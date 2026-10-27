@php($tel = 'tel:'.$site['phone_href'])
<header class="header" id="top">
    <div class="header__bar">
        <div class="header__inner">
            <button class="header__burger" type="button" aria-label="Открыть меню" aria-controls="drawer" aria-expanded="false" data-drawer-open>
                <span></span><span></span><span></span>
            </button>

            <a class="brand" href="{{ route('home') }}" aria-label="{{ $site['brand_plain'] }} — на главную">
                <span class="brand__logo">{{ $site['brand'] }}</span>
                <span class="brand__tagline">{{ $site['tagline'] }}</span>
            </a>

            <div class="header__call">
                <a class="header__phone" href="{{ $tel }}">@include('partials.icon', ['name' => 'phone']) {{ $site['phone'] }}</a>
                <div class="header__call-row">
                    <a class="btn btn--red header__call-btn" href="{{ $tel }}">Позвонить</a>
                    <a class="msg msg--tg" href="{{ $site['telegram'] }}" target="_blank" rel="noopener" aria-label="Написать в Telegram">@include('partials.icon', ['name' => 'telegram'])</a>
                    <a class="msg msg--wa" href="{{ $site['whatsapp'] }}" target="_blank" rel="noopener" aria-label="Написать в WhatsApp">@include('partials.icon', ['name' => 'whatsapp'])</a>
                </div>
            </div>

            <address class="header__address">
                <span>{{ $site['city'] }},</span>
                <span>{{ $site['address_short'] }}</span>
                <span>◴ {{ $site['hours_short'] }}</span>
            </address>

            <a class="header__phone-icon" href="{{ $tel }}" aria-label="Позвонить {{ $site['phone'] }}">@include('partials.icon', ['name' => 'phone'])</a>
        </div>
    </div>

    <div class="header__nav-bar">
        <div class="container header__nav-inner">
            <nav aria-label="Основное меню">
                @include('partials.nav', ['variant' => 'desktop'])
            </nav>
            <a class="btn btn--red btn--sm" href="#lead" data-open-lead>Записаться</a>
        </div>
    </div>
</header>

<div class="drawer" id="drawer" aria-hidden="true" data-drawer>
    <div class="drawer__overlay" data-drawer-close></div>
    <aside class="drawer__panel" role="dialog" aria-modal="true" aria-label="Меню">
        <div class="drawer__head">
            <a class="brand brand--sm" href="{{ route('home') }}">
                <span class="brand__logo">{{ $site['brand'] }}</span>
                <span class="brand__tagline">{{ $site['tagline'] }}</span>
            </a>
            <button class="drawer__close" type="button" aria-label="Закрыть меню" data-drawer-close>@include('partials.icon', ['name' => 'close'])</button>
        </div>

        <nav class="drawer__nav" aria-label="Мобильное меню">
            @include('partials.nav', ['variant' => 'drawer'])
        </nav>

        <div class="drawer__contacts">
            <a class="drawer__phone" href="{{ $tel }}">{{ $site['phone'] }}</a>
            <div class="drawer__msgs">
                <a class="msg msg--tg" href="{{ $site['telegram'] }}" target="_blank" rel="noopener" aria-label="Telegram">@include('partials.icon', ['name' => 'telegram'])</a>
                <a class="msg msg--wa" href="{{ $site['whatsapp'] }}" target="_blank" rel="noopener" aria-label="WhatsApp">@include('partials.icon', ['name' => 'whatsapp'])</a>
            </div>
            <p>@include('partials.icon', ['name' => 'pin']) {{ $site['address_full'] }}</p>
            <p>@include('partials.icon', ['name' => 'clock']) {{ $site['hours_short'] }}, {{ mb_strtolower($site['day_off']) }}</p>
            <a class="btn btn--red drawer__cta" href="#lead" data-open-lead>Записаться</a>
        </div>
    </aside>
</div>
