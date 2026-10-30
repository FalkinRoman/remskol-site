<picture>
    <source srcset="{{ img_asset('images/'.$name.'.webp') }}" type="image/webp">
    <img src="{{ img_asset('images/'.$name.'.jpg') }}"
         alt="{{ $alt }}"
         width="{{ $width }}" height="{{ $height }}"
         class="{{ $class ?? '' }}"
         @if ($eager ?? false) fetchpriority="high" @else loading="lazy" @endif
         decoding="async">
</picture>
