<dialog class="modal" id="lead" aria-labelledby="lead-title" @if (session('lead_form') === 'modal' || old('form_id') === 'modal') data-autoopen @endif>
    <div class="modal__box">
        <button class="modal__close" type="button" aria-label="Закрыть" data-close-lead>@include('partials.icon', ['name' => 'close'])</button>
        <h2 class="modal__title" id="lead-title">Записаться на ремонт</h2>
        <p class="modal__lead">Перезвоним, подскажем цену и подберём удобное время. Можно приложить фото скола — оценим заранее.</p>
        @include('partials.lead-form', ['prefix' => 'modal'])
        <p class="modal__alt">или сразу:
            <a href="tel:{{ $site['phone_href'] }}">{{ $site['phone'] }}</a> ·
            <a href="{{ $site['telegram'] }}" target="_blank" rel="noopener">Telegram</a> ·
            <a href="{{ $site['whatsapp'] }}" target="_blank" rel="noopener">WhatsApp</a>
        </p>
    </div>
</dialog>
