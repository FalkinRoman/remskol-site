<div class="price-card">
    @isset($heading)
        @php($tag = $headingTag ?? 'h3')
        <{{ $tag }} class="price-card__title">
            @isset($href)<a href="{{ $href }}">{{ $heading }}</a>@else{{ $heading }}@endisset
        </{{ $tag }}>
    @endisset
    <table class="price-table">
        <thead class="visually-hidden">
            <tr><th scope="col">Услуга</th><th scope="col">Цена</th></tr>
        </thead>
        <tbody>
            @foreach ($prices as $p)
                <tr>
                    <th scope="row">{{ $p['name'] }}</th>
                    <td>{{ nb($p['price']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @isset($note)
        <p class="price-card__note">{{ nb($note) }}</p>
    @endisset
    @isset($service)
        <a class="btn btn--red price-card__btn" href="#lead" data-open-lead data-service="{{ $service }}" data-comment="Хочу узнать точную цену">Узнать точную цену</a>
    @endisset
</div>
