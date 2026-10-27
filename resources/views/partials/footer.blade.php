@php($tel = 'tel:'.$site['phone_href'])
<footer class="footer" id="contacts">
    <div class="container">
        @php($withMap = ! request()->routeIs('contacts'))
        <div class="footer__top {{ $withMap ? '' : 'footer__top--solo' }}">
            @if ($withMap)
                <div class="footer__map">
                    <iframe src="{{ $site['map_iframe'] }}" title="Карта: {{ $site['address_full'] }}" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            @endif

            <div class="footer__col">
                <h2 class="footer__title">Контакты</h2>
                <p class="footer__name">{{ $site['brand_plain'] }} — {{ mb_strtolower($site['tagline']) }}</p>
                <address class="footer__contacts">
                    <a class="footer__phone" href="{{ $tel }}">@include('partials.icon', ['name' => 'phone']) {{ $site['phone'] }}</a>
                    <span>@include('partials.icon', ['name' => 'pin']) <a href="{{ $site['map_link'] }}" target="_blank" rel="noopener">{{ $site['address_full'] }}</a></span>
                    <span>@include('partials.icon', ['name' => 'clock']) {{ $site['hours_long'] }}</span>
                    <span class="footer__muted">{{ $site['day_off'] }}</span>
                    @if ($site['legal']['email'])
                        <a href="mailto:{{ $site['legal']['email'] }}">{{ $site['legal']['email'] }}</a>
                    @endif
                </address>
                <div class="footer__msgs">
                    <a class="footer__msg footer__msg--tg" href="{{ $site['telegram'] }}" target="_blank" rel="noopener">@include('partials.icon', ['name' => 'telegram']) Telegram</a>
                    <a class="footer__msg footer__msg--wa" href="{{ $site['whatsapp'] }}" target="_blank" rel="noopener">@include('partials.icon', ['name' => 'whatsapp']) WhatsApp</a>
                </div>
                <a class="footer__rating" href="{{ $site['map_link'] }}" target="_blank" rel="noopener">
                    <img src="{{ asset('images/yandex-rating.png') }}" alt="Рейтинг организации в Яндексе 5,0" width="148" height="46" loading="lazy">
                </a>
            </div>
        </div>

        <div class="footer__bottom">
            <div class="footer__legal">
                <p>© {{ date('Y') }} {{ $site['brand_plain'] }}. {{ $site['legal']['status'] }} {{ $site['legal']['name'] }}, ИНН {{ $site['legal']['inn'] }}@if ($site['legal']['ogrnip']), ОГРНИП {{ $site['legal']['ogrnip'] }}@endif.</p>
                <p>Информация на сайте носит справочный характер и не является публичной офертой (ст. 437 ГК РФ).</p>
            </div>
            <ul class="footer__docs">
                <li><a href="{{ route('privacy') }}">Политика конфиденциальности</a></li>
                <li><a href="{{ route('consent') }}">Согласие на обработку персональных данных</a></li>
            </ul>
        </div>
    </div>
</footer>
