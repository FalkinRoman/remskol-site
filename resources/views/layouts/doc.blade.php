@extends('layouts.app')

@section('content')
    <header class="doc-header">
        <div class="container doc-header__inner">
            <a class="brand brand--sm" href="{{ url('/') }}">
                <span class="brand__logo">{{ $site['brand'] }}</span>
                <span class="brand__tagline">{{ $site['tagline'] }}</span>
            </a>
            <a class="doc-header__back" href="{{ url('/') }}">← На главную</a>
        </div>
    </header>

    <main class="page">
        <div class="container page__inner">
            @yield('doc')
        </div>
    </main>

    <footer class="footer footer--doc">
        <div class="container footer__legal">
            <p>© {{ date('Y') }} {{ $site['brand_plain'] }}. {{ $site['legal']['status'] }} {{ $site['legal']['name'] }}, ИНН {{ $site['legal']['inn'] }}.</p>
        </div>
    </footer>
@endsection
