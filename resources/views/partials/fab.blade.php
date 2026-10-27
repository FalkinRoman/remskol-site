<div class="fab" data-fab>
    <div class="fab__menu" id="fab-menu" role="menu">
        <a class="fab__item fab__item--tg" href="{{ $site['telegram'] }}" target="_blank" rel="noopener" role="menuitem" data-goal="fab_telegram">
            <span class="fab__label">Telegram</span><span class="fab__icon">@include('partials.icon', ['name' => 'telegram'])</span>
        </a>
        <a class="fab__item fab__item--wa" href="{{ $site['whatsapp'] }}" target="_blank" rel="noopener" role="menuitem" data-goal="fab_whatsapp">
            <span class="fab__label">WhatsApp</span><span class="fab__icon">@include('partials.icon', ['name' => 'whatsapp'])</span>
        </a>
        <a class="fab__item fab__item--call" href="tel:{{ $site['phone_href'] }}" role="menuitem" data-goal="fab_call">
            <span class="fab__label">Позвонить</span><span class="fab__icon">@include('partials.icon', ['name' => 'phone'])</span>
        </a>
    </div>
    <button class="fab__toggle" type="button" aria-label="Связаться с нами" aria-expanded="false" aria-controls="fab-menu" data-fab-toggle>
        <span class="fab__ring" aria-hidden="true"></span>
        @include('partials.icon', ['name' => 'phone', 'class' => 'fab__phone'])
        @include('partials.icon', ['name' => 'close', 'class' => 'fab__close'])
    </button>
</div>
