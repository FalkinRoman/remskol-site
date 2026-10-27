@php($is = fn (string $route, ?string $slug = null) => request()->routeIs($route) && ($slug === null || request()->route('slug') === $slug))
<ul class="menu menu--{{ $variant }}">
    <li class="menu__item menu__item--has-sub">
        @if ($variant === 'drawer')
            <button class="menu__link menu__toggle" type="button" aria-expanded="{{ request()->routeIs('service') ? 'true' : 'false' }}" data-submenu-toggle>
                Услуги <img class="menu__chevron" src="{{ asset('images/icons/chevron.svg') }}" alt="" width="16" height="16">
            </button>
        @else
            <a class="menu__link {{ request()->routeIs('service') ? 'is-active' : '' }}" href="{{ route('home') }}#services">Услуги
                <img class="menu__chevron" src="{{ asset('images/icons/chevron.svg') }}" alt="" width="16" height="16">
            </a>
        @endif
        <ul class="menu__sub">
            @foreach ($catalog['services'] as $slug => $service)
                <li><a href="{{ route('service', $slug) }}" @if ($is('service', $slug)) aria-current="page" @endif>{{ $service['title'] }}</a></li>
            @endforeach
        </ul>
    </li>
    <li class="menu__item"><a class="menu__link {{ $is('works') ? 'is-active' : '' }}" href="{{ route('works') }}" @if ($is('works')) aria-current="page" @endif>Примеры работ</a></li>
    <li class="menu__item"><a class="menu__link {{ $is('prices') ? 'is-active' : '' }}" href="{{ route('prices') }}" @if ($is('prices')) aria-current="page" @endif>Цены</a></li>
    <li class="menu__item"><a class="menu__link {{ $is('contacts') ? 'is-active' : '' }}" href="{{ route('contacts') }}" @if ($is('contacts')) aria-current="page" @endif>Контакты</a></li>
</ul>
