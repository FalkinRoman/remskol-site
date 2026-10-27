@php($p = $prefix)
@php($ok = session('lead_success') && session('lead_form') === $p)
@php($err = $errors->any() && old('form_id') === $p)
<form class="lead-form {{ $class ?? '' }}" method="post" action="{{ route('lead.store') }}" enctype="multipart/form-data" novalidate data-lead-form>
    @csrf
    <input type="hidden" name="form_id" value="{{ $p }}">

    <div class="lead-form__success" data-success @unless ($ok) hidden @endunless role="status">
        <strong>Заявка принята!</strong>
        <span>Перезвоним в ближайшее рабочее время ({{ $site['hours_short'] }}).</span>
    </div>

    <div class="lead-form__fields" data-fields @if ($ok) hidden @endif>
        <div class="lead-form__row">
            <div class="field">
                <label class="field__label" for="{{ $p }}-name">Имя *</label>
                <input class="field__input" id="{{ $p }}-name" type="text" name="name" value="{{ $err ? old('name') : '' }}" autocomplete="name" required maxlength="100" placeholder="Как к вам обращаться">
                <span class="field__error" data-error="name">{{ $err ? $errors->first('name') : '' }}</span>
            </div>
            <div class="field">
                <label class="field__label" for="{{ $p }}-phone">Телефон *</label>
                <input class="field__input" id="{{ $p }}-phone" type="tel" name="phone" value="{{ $err ? old('phone') : '' }}" autocomplete="tel" inputmode="tel" placeholder="+7 (___) ___-__-__" required maxlength="32" data-phone>
                <span class="field__error" data-error="phone">{{ $err ? $errors->first('phone') : '' }}</span>
            </div>
        </div>

        <div class="field">
            <label class="field__label" for="{{ $p }}-service">Услуга</label>
            <select class="field__input" id="{{ $p }}-service" name="service" data-service-select>
                @foreach ($site['services'] as $service)
                    <option value="{{ $service['title'] }}">{{ $service['title'] }}</option>
                @endforeach
                <option value="Узнать цену">Узнать цену</option>
                <option value="Другое">Другое</option>
            </select>
        </div>

        <div class="field">
            <label class="field__label" for="{{ $p }}-comment">Сообщение</label>
            <textarea class="field__input" id="{{ $p }}-comment" name="comment" rows="2" maxlength="1000" placeholder="Марка авто, размер скола, удобное время">{{ $err ? old('comment') : '' }}</textarea>
        </div>

        <label class="file" data-file>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp,image/heic,image/heif" data-file-input>
            @include('partials.icon', ['name' => 'camera'])
            <span class="file__text" data-file-text>Фото скола <small>(не обязательно)</small></span>
        </label>
        <span class="field__error" data-error="photo">{{ $err ? $errors->first('photo') : '' }}</span>

        <label class="lead-form__hp" aria-hidden="true">Сайт <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        <input type="hidden" name="ts" value="" data-ts>
        <input type="hidden" name="utm" value="" data-utm>

        <label class="checkbox">
            <input type="checkbox" name="agree" value="1" required>
            <span>Даю <a href="{{ route('consent') }}" target="_blank">согласие на обработку персональных данных</a> и ознакомлен(а) с <a href="{{ route('privacy') }}" target="_blank">политикой конфиденциальности</a></span>
        </label>
        <span class="field__error" data-error="agree">{{ $err ? $errors->first('agree') : '' }}</span>

        <button class="btn btn--red lead-form__submit" type="submit">{{ $submit ?? 'Отправить заявку' }}</button>
    </div>
</form>
